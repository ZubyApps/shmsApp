<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Patient;
use App\Jobs\SendFormLink;
use Carbon\CarbonImmutable;
use App\Jobs\SendCardNumber;
use Illuminate\Http\Request;
use App\Models\PatientPreForm;
use App\Services\HelperService;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\DataObjects\FormLinkParams;
use App\DataObjects\DataTableQueryParams;
use Illuminate\Database\Eloquent\Builder;

class PatientService
{
    public function __construct(
        private readonly Patient $patient, 
        private readonly PatientPreForm $patientPreForm,
        )
    {
    }

    public function create(Request $data, User $user): Patient
    {
        return DB::transaction(function () use($data, $user){
            $patient = $user->patients()->create([
                    "patient_type"          => $data->patientType,
                    "address"               => $data->address,
                    "blood_group"           => $data->bloodGroup,
                    "card_no"               => $data->cardNumber,
                    "date_of_birth"         => $data->dateOfBirth,
                    "email"                 => $data->email,
                    "ethnic_group"          => $data->ethnicGroup,
                    "first_name"            => $data->firstName,
                    "genotype"              => $data->genotype,
                    "known_conditions"      => $data->knownConditions,
                    "last_name"             => $data->lastName,
                    "marital_status"        => $data->maritalStatus,
                    "middle_name"           => $data->middleName,
                    "nationality"           => $data->nationality,
                    "next_of_kin"           => $data->nextOfKin,
                    "next_of_kin_phone"     => $data->nextOfKinPhone,
                    "next_of_kin_rship"     => $data->nextOfKinRship,
                    "occupation"            => $data->occupation,
                    "phone"                 => $data->phone,
                    "registration_bill"     => $data->registrationBill,
                    "religion"              => $data->religion,
                    "sex"                   => $data->sex,
                    "sms"                   => $data->sms,
                    "sponsor_id"            => $data->sponsor,
                    "staff_Id"              => $data->staffId,
                    "flag"                  => $data->flagPatient,
                    "flag_reason"           => $data->flagReason,
                    "flagged_by"            => $data->flagReason ? $user->id : null,
                    "flagged_at"            => $data->flagReason ? new Carbon() : null,
                    "state_of_origin"       => $data->stateOrigin,
                    "state_of_residence"    => $data->stateResidence,
            ]);

            if ($data->prePatient){
                $this->deletePrePatient((int)$data->prePatient);
            }

            if ((new HelperService)->nccTextTime() && $patient->canSms()){
                SendCardNumber::dispatch($patient)->delay(5);
            }

            return $patient;
        });
    }

    public function update(Request $data, Patient $patient, User $user): Patient
    {    
        $data->validate(['cardNumber' =>  Rule::unique('patients', 'card_no')->ignore($patient->id)], ['cardNumber.unique' => "This isn't this patients original number and it belongs to another patient"]);
   
        $cardNumber = $user->designation->access_level > 4 && $patient->card_no != $data->cardNumber;
        $newFlagger = $data->flagPatient && ($patient->flag_reason !== $data->flagReason);
        $patient->update([
                "patient_type"          => $data->patientType,
                "address"               => $data->address,
                "blood_group"           => $data->bloodGroup,
                "date_of_birth"         => $data->dateOfBirth,
                "email"                 => $data->email,
                "ethnic_group"          => $data->ethnicGroup,
                "first_name"            => $data->firstName,
                "genotype"              => $data->genotype,
                "known_conditions"      => $data->knownConditions,
                "last_name"             => $data->lastName,
                "marital_status"        => $data->maritalStatus,
                "middle_name"           => $data->middleName,
                "nationality"           => $data->nationality,
                "next_of_kin"           => $data->nextOfKin,
                "next_of_kin_phone"     => $data->nextOfKinPhone,
                "next_of_kin_rship"     => $data->nextOfKinRship,
                "occupation"            => $data->occupation,
                "phone"                 => $data->phone,
                "registration_bill"     => $data->registrationBill,
                "religion"              => $data->religion,
                "sex"                   => $data->sex,
                "sms"                   => $data->sms,
                "sponsor_id"            => $data->sponsor,
                "staff_id"              => $data->staffId,
                "flag"                  => $data->flagPatient,
                "flag_reason"           => $data->flagReason,
                "flagged_by"            => $newFlagger ? $user->id : $patient->flagged_by,
                "flagged_at"            => $newFlagger ? new Carbon() : $patient->flagged_at,
                "state_of_origin"       => $data->stateOrigin,
                "state_of_residence"    => $data->stateResidence,

        ]);

        if ($cardNumber){$patient->update(["card_no" => $data->cardNumber]);}

        return $patient;
    }

    public function updateKnownClinicalInfo(Request $data, Patient $patient, User $user): Patient
    {
        // Prepare updates array
        $updates = [];

        // Update blood group if provided and not empty
        if ($data->filled('bloodGroup')) {
            $updates['blood_group'] = $data->bloodGroup;
        }

        // Update genotype if provided and not empty
        if ($data->filled('genotype')) {
            $updates['genotype'] = $data->genotype;
        }

        // Handle known conditions: append if present, start fresh if none exist, skip if empty
        if ($data->filled('knownConditions')) {
            $updates['known_conditions'] = $patient->known_conditions 
                ? $patient->known_conditions . ', ' . $user->username . ' - ' . $data->knownConditions
                : $user->username . ' - ' . $data->knownConditions;
        }

        // Perform single update if there are changes
        if (!empty($updates)) {
            $patient->update($updates);
        }
        
        return $patient;
    }

    public function sendFormLink($data, User $user)
    {
        $formLinkParams  = new FormLinkParams(
            config('app.url').'/form', 
            (int)$data->sponsorCategory, 
            (int)$data->sponsor, 
            $data->cardNumber, 
            $data->patientType, 
            $data->phone, 
            $user->id
        );

        $patientForm = $this->patientPreForm->create(
                [
                    'sponsor_category'  => $formLinkParams->sponsorCat,
                    'sponsor_id'        => $formLinkParams->sponsor,
                    'card_no'           => $formLinkParams->cardNumber,
                    'patient_type'      => $formLinkParams->patientType,
                    'phone'             => $formLinkParams->phone,
                    'user_id'           => $formLinkParams->userId,
                    'id'                => rand(0000, 9999)
                ]
            );

        $link = route('patientForm', ['patientPreForm' => $patientForm->id]);

        if ((new HelperService)->nccTextTime()){
            SendFormLink::dispatch($link, $formLinkParams);
        }

        return response()->json(['message' => 'Form link prepared and queued successfully'], 200);
    }

    public function getPaginatedPatients(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->patient->select('id', 'sponsor_id', 'user_id', 'flag', 'flag_reason', 'first_name', 'middle_name', 'last_name', 'card_no', 'date_of_birth', 'phone', 'sex', 'is_active', 'created_at', 'flagged_by', 'flagged_at')
                        ->with([
                            'user:id,username', 
                            'sponsor:id,name,category_name,flag',
                            'flaggedBy:id,username'
                            ])
                        ->withExists(['visits as hasVisits']);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                        $query->whereRaw('CONCAT_WS(" ", first_name, middle_name, last_name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('CONCAT_WS(" ", first_name, last_name, middle_name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('CONCAT_WS(" ", last_name, middle_name, first_name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('CONCAT_WS(" ", last_name, first_name, middle_name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('CONCAT_WS(" ", middle_name, first_name, last_name) LIKE ?', [$searchTerm])
                            ->orWhereRaw('CONCAT_WS(" ", middle_name, last_name, first_name) LIKE ?', [$searchTerm])
                            ->orWhere('card_no', 'LIKE', $searchTerm)
                            ->orWhere('phone', 'LIKE', $searchTerm)
                            ->orWhere('sex', 'LIKE', $searchTerm)
                            ->orWhere('date_of_birth', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm )
                            ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', $searchTerm );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            
            // $search   = trim($params->searchTerm);
            // $likeTerm = '%' . addcslashes($search, '%_') . '%';

            // Build boolean full-text term: "ike* chi*"
            // $words       = array_filter(explode(' ', $search));
            // $booleanTerm = implode(' ', array_map(fn($w) => $w . '*', $words));

            // return $query->where(function (Builder $q) use ($likeTerm, $booleanTerm) {

                // 1. NAME: Full-text search (any order, partial)
                // $q->whereFullText(['first_name', 'middle_name', 'last_name'], $booleanTerm, ['mode' => 'boolean']);

                // 2. CARD NO & PHONE
                // $q->orWhere('card_no', 'LIKE', $likeTerm)
                // ->orWhere('phone', 'LIKE', $likeTerm);

                // 4. SPONSOR & CATEGORY (already unique → indexed)
                // $q->orWhereRelation('sponsor', 'name', 'LIKE', $likeTerm)
                // ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', $likeTerm);

            // })
            // ->orderBy($orderBy, $orderDir)
            // ->paginate(
            //     $params->length,
            //     ['*'],
            //     'page',
            //     ceil(($params->start + $params->length) / $params->length)
            // );

        }

        if ($data->filterBy == 'flaggedPatients'){
            return $query->where('flag', true)
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Patient $patient) {
            return [
                'id'                => $patient->id,
                'card'              => $patient->card_no,
                'name'              => $patient->fullName(),
                'phone'             => $patient->phone,
                'sex'               => $patient->sex,
                'age'               => $patient->age(),
                'sponsor'           => $patient->sponsor->name,
                'category'          => $patient->sponsor->category_name,
                'flagSponsor'       => $patient->sponsor->flag,
                'flagPatient'       => $patient->flag,
                'flagReason'        => $patient->flag_reason,
                'createdAt'         => (new Carbon($patient->created_at))->format('d/m/Y'),
                'createdBy'         => $patient->user?->username,
                'active'            => $patient->is_active,
                'hasVisits'         => $patient->hasVisits,
                'patient'           => $patient->patientId(),
                'flaggedBy'         => $patient->flaggedBy?->username,
                'flaggedAt'         => $patient->flagged_at ? (new Carbon($patient->flagged_at))->format('d/m/y g:ia') : '',
                'count'             => $patient?->visitsCount
            ];
         };
    }

    public function deletePrePatient(int $id)
    {
        return $this->patientPreForm->destroy($id);
    }

    public function getSummaryBySex(DataTableQueryParams $params, $data)
    {
        return DB::table('patients')
            ->selectRaw('sex, COUNT(patients.id) as patientsCount')
            ->groupBy('sex')
            ->orderBy('patientsCount', 'desc')
            ->get()
            ->toArray();

    }

    public function getSummaryByAge(DataTableQueryParams $params, $data)
    {
        $currentDate = new CarbonImmutable();
        
        return DB::table('patients')
            ->selectRaw("
            SUM(CASE WHEN DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) AS zeroTo3m, 
            SUM(CASE WHEN (DATE(date_of_birth) < DATE_SUB(CURDATE(), INTERVAL 3 MONTH) AND DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)) THEN 1 ELSE 0 END) AS threeTo12m,
            SUM(CASE WHEN (DATE(date_of_birth) < DATE_SUB(CURDATE(), INTERVAL 12 MONTH) AND DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 60 MONTH)) THEN 1 ELSE 0 END) AS oneTo5yrs,
            SUM(CASE WHEN (DATE(date_of_birth) < DATE_SUB(CURDATE(), INTERVAL 60 MONTH) AND DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 156 MONTH)) THEN 1 ELSE 0 END) AS fiveto13yrs,
            SUM(CASE WHEN (DATE(date_of_birth) < DATE_SUB(CURDATE(), INTERVAL 156 MONTH) AND DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 216 MONTH)) THEN 1 ELSE 0 END) AS thirteenTo18yrs,
            SUM(CASE WHEN (DATE(date_of_birth) < DATE_SUB(CURDATE(), INTERVAL 216 MONTH) AND DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 576 MONTH)) THEN 1 ELSE 0 END) AS eighteenTo48yrs,
            SUM(CASE WHEN (DATE(date_of_birth) < DATE_SUB(CURDATE(), INTERVAL 576 MONTH) AND DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 756 MONTH)) THEN 1 ELSE 0 END) AS fortyEightTo63yrs,
            SUM(CASE WHEN (DATE(date_of_birth) < DATE_SUB(CURDATE(), INTERVAL 756 MONTH)) THEN 1 ELSE 0 END) AS above63yrs, sex"
            )
            ->groupBy('sex')
            ->get()
            ->toArray();
    }

    public function getNewRegSummaryBySponsor(DataTableQueryParams $params, $data)
    {
        $current = Carbon::now();

        if (! empty($params->searchTerm)) {

            if($data->date){
                $date = new Carbon($data->date);

                return DB::table('patients')
                ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(DISTINCT patients.id) as patientsCount')
                ->leftJoin('sponsors', 'patients.sponsor_id', '=', 'sponsors.id')
                ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
                ->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                ->orWhere('sponsor_categories.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                ->whereMonth('patients.created_at', $date->month)
                ->whereYear('patients.created_at', $date->year)
                ->groupBy('sponsor', 'id', 'category')
                ->orderBy('sponsor')
                ->orderBy('patientsCount')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return DB::table('patients')
            ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(DISTINCT patients.id) as patientsCount')
            ->leftJoin('sponsors', 'patients.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
            ->orWhere('sponsor_categories.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
            ->whereMonth('patients.created_at', $current->month)
            ->whereYear('patients.created_at', $current->year)
            ->groupBy('sponsor', 'id', 'category')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return DB::table('patients')
            ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(DISTINCT patients.id) as patientsCount')
            ->leftJoin('sponsors', 'patients.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->whereMonth('patients.created_at', $date->month)
            ->whereYear('patients.created_at', $date->year)
            ->groupBy('sponsor', 'id', 'category')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return DB::table('patients')
            ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(DISTINCT patients.id) as patientsCount')
            ->leftJoin('sponsors', 'patients.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->whereMonth('patients.created_at', $current->month)
            ->whereYear('patients.created_at', $current->year)
            ->groupBy('sponsor', 'id', 'category')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPatientsBySponsor(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current = Carbon::now();
        $query      = $this->patient->select('id', 'sponsor_id', 'user_id', 'flag', 'flag_reason', 'first_name', 'middle_name', 'last_name', 'card_no', 'date_of_birth', 'phone', 'sex', 'is_active', 'created_at', 'flagged_by', 'flagged_at')
                ->with(['user:id,username', 'sponsor:id,name,category_name'])
                ->withCount(['visits as visitsCount']);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            if($data->date){
                $date = new Carbon($data->date);

                return $query->where('sponsor_id', $data->sponsorId)
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->where('first_name', 'LIKE', $searchTerm)
                            ->orWhere('middle_name', 'LIKE', $searchTerm)
                            ->orWhere('last_name', 'LIKE', $searchTerm)
                            ->orWhere('card_no', 'LIKE', $searchTerm)
                            ->orWhere('phone', 'LIKE', $searchTerm)
                            ->orWhere('sex', 'LIKE', $searchTerm);
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('sponsor_id', $data->sponsorId)
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->where('first_name', 'LIKE', $searchTerm)
                            ->orWhere('middle_name', 'LIKE', $searchTerm)
                            ->orWhere('last_name', 'LIKE', $searchTerm)
                            ->orWhere('card_no', 'LIKE', $searchTerm)
                            ->orWhere('phone', 'LIKE', $searchTerm)
                            ->orWhere('sex', 'LIKE', $searchTerm);
                        })
                        ->whereMonth('created_at', $current->month)
                        ->whereYear('created_at', $current->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return $query->where('sponsor_id', $data->sponsorId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        
        return $query->where('sponsor_id', $data->sponsorId)
                ->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    // public function patientList($data)
    // {
    //     $query = $this->patient->select('id', 'first_name', 'middle_name', 'last_name', 'card_no', 'phone', 'sponsor_id')
    //                     ->with(['sponsor:id,name']);

    //     if (! empty($data->fullId)){
    //         $searchTerm = '%' . addcslashes($data->fullId, '%_') . '%';
    //         if ($data->type == 'ANC'){
    //             return $query->whereRelation('visits', 'visit_type', 'ANC')
    //                     ->where(function (Builder $query) use($searchTerm) {
    //                         $query->whereRaw('CONCAT_WS(" ", first_name, middle_name, last_name) LIKE ?', [$searchTerm])
    //                             ->orWhereRaw('CONCAT_WS(" ", last_name, middle_name, first_name) LIKE ?', [$searchTerm])
    //                             ->orWhereRaw('CONCAT_WS(" ", first_name, last_name, middle_name) LIKE ?', [$searchTerm])
    //                             ->orWhereRaw('CONCAT_WS(" ", last_name, first_name, middle_name) LIKE ?', [$searchTerm])
    //                             ->orWhere('card_no', 'LIKE', $searchTerm)
    //                             ->orWhere('phone', 'LIKE', $searchTerm);
    //                     })
    //                     ->orderBy('created_at', 'asc')
    //                     ->get(['first_name', 'middle_name', 'last_name', 'card_no', 'sponsor_id', 'phone']);
    //         }
    //         return $query->where(function (Builder $query) use($searchTerm) {
    //                         $query->whereRaw('CONCAT_WS(" ", first_name, middle_name, last_name) LIKE ?', [$searchTerm])
    //                             ->orWhereRaw('CONCAT_WS(" ", last_name, middle_name, first_name) LIKE ?', [$searchTerm])
    //                             ->orWhereRaw('CONCAT_WS(" ", first_name, last_name, middle_name) LIKE ?', [$searchTerm])
    //                             ->orWhereRaw('CONCAT_WS(" ", last_name, first_name, middle_name) LIKE ?', [$searchTerm]);
    //                     })
    //                     ->orWhere('card_no', 'LIKE', $searchTerm )
    //                     ->orWhere('phone', 'LIKE', $searchTerm )
    //                     ->orderBy('created_at', 'asc')
    //                     ->get(['first_name', 'middle_name', 'last_name', 'card_no', 'sponsor_id', 'phone']);

    //         // $search    = trim($data->fullId);
    //         // $likeTerm  = '%' . addcslashes($search, '%_') . '%';

    //         // Build full-text boolean term: "ike* chi*"
    //         // $words       = array_filter(explode(' ', $search));
    //         // $booleanTerm = implode(' ', array_map(fn($w) => $w . '*', $words));

    //         // $query = $this->patient->newQuery();

    //         // Apply ANC filter if needed
    //         // if ($data->type === 'ANC') {
    //         //     $query->whereRelation('visits', 'visit_type', 'ANC');
    //         // }

    //         // Main OR search
    //         // $query->where(function (Builder $q) use ($booleanTerm, $likeTerm) {
    //             // 1. NAME: Full-text search (any order, partial)
    //             // $q->whereFullText(
    //             //     ['first_name', 'middle_name', 'last_name'],
    //             //     $booleanTerm,
    //             //     ['mode' => 'boolean']
    //             // );

    //             // 2. CARD NO & PHONE
    //             // $q->orWhere('card_no', 'LIKE', $likeTerm)
    //             // ->orWhere('phone', 'LIKE', $likeTerm);
    //         // });

    //         // return $query
    //         //     ->orderBy('created_at', 'asc')
    //         //     ->get([
    //         //         'first_name', 'middle_name', 'last_name',
    //         //         'card_no', 'sponsor_id', 'phone'
    //         //     ]);
    //     }      
    // }

    public function patientList($data)
    {
        if (! empty($data->fullId)){
            $searchTerm = '%' . addcslashes($data->fullId, '%_') . '%';

            $query = $this->patient->newQuery();

            if ($data->type === 'ANC') {
                $query->whereRelation('visits', 'visit_type', 'ANC');
            }
            $query = $this->scopeSearchByName($query, $searchTerm);
            $query->orWhere('card_no', 'LIKE', $searchTerm)
                ->orWhere('phone', 'LIKE', $searchTerm);

            return $query
                    ->orderBy('created_at', 'asc')
                    ->get([
                       'first_name', 'middle_name', 'last_name',
                       'card_no', 'sponsor_id', 'phone', 'id'
                   ]);
;        }      
    }

    public function listTransformer()
    {
        return function (Patient $patient){
            return [
                'fullId'    => $patient->patientId() .' ('. $patient->phone. ') ('. $patient->sponsor->name. ')',
                'cardNo'    => $patient->card_no,
                'id'        => $patient->id,
            ];
        };
    }

    public function scopeSearchByName($query, $search)
    {
        $terms = array_filter(explode(' ', trim($search)));
        
        if (empty($terms)) {
            return $query;
        }
        
        return $query->where(function($q) use ($terms) {
            foreach ($terms as $term) {
                $q->where(function($subQuery) use ($term) {
                    $subQuery->where('first_name', 'LIKE', $term)
                            ->orWhere('middle_name', 'LIKE', $term)
                            ->orWhere('last_name', 'LIKE', $term);
                });
            }
        });
    }
}