<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\DataObjects\FormLinkParams;
use App\Jobs\SendCardNumber;
use App\Jobs\SendFormLink;
use App\Models\Patient;
use App\Models\PatientPreForm;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class PatientService
{
    public function __construct(
        private readonly Patient $patient, 
        private readonly HelperService $helperService, 
        private readonly PatientPreForm $patientPreForm,
        private readonly ChurchPlusSmsService $churchPlusSmsService
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
                    "state_of_origin"       => $data->stateOrigin,
                    "state_of_residence"    => $data->stateResidence,
            ]);

            if ($data->prePatient){
                $this->deletePrePatient((int)$data->prePatient);
            }

            if ($patient->sms){
                SendCardNumber::dispatch($patient)->delay(5);
            }

            return $patient;
        });
    }

    public function update(Request $data, Patient $patient, User $user): Patient
    {    
        $data->validate(['cardNumber' =>  Rule::unique('patients', 'card_no')->ignore($patient->id)], ['cardNumber.unique' => "This isn't this patients original number and it belongs to another patient"]);
   
        $cardNumber = $user->designation->access_level > 4 && $patient->card_no != $data->cardNumber;
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
                "state_of_origin"       => $data->stateOrigin,
                "state_of_residence"    => $data->stateResidence,

        ]);

        if ($cardNumber){$patient->update(["card_no" => $data->cardNumber]);}

        return $patient;
    }

    public function updateKnownClinicalInfo(Request $data, Patient $patient, User $user): Patient
    {
        $knownConditions = $patient->known_conditions ? $patient->known_conditions.', '.$data->knownConditions : $data->knownConditions;
        $data->bloodGroup ? $patient->update(["blood_group" => $data->bloodGroup]) : '';
        $data->genotype ? $patient->update(["genotype" => $data->genotype]): '';
        $data->knownConditions ? $patient->update(["known_conditions" => $knownConditions]) : '';
        
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

        SendFormLink::dispatch($link, $formLinkParams);
        return response()->json(['message' => 'Form link prepared and queued successfully'], 200);
    }

    public function getPaginatedPatients(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->patient::with(['user', 'sponsor.sponsorCategory', 'visits']);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where('first_name', 'LIKE', $searchTerm )
                        ->orWhere('middle_name', 'LIKE', $searchTerm )
                        ->orWhere('last_name', 'LIKE', $searchTerm )
                        ->orWhere('card_no', 'LIKE', $searchTerm )
                        ->orWhere('phone', 'LIKE', $searchTerm )
                        ->orWhere('sex', 'LIKE', $searchTerm)
                        ->orWhere('date_of_birth', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm )
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', $searchTerm )
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
                'age'               => $this->helperService->twoPartDiffInTimePast($patient->date_of_birth),
                'sponsor'           => $patient->sponsor->name,
                'category'          => $patient->sponsor->sponsorCategory->name,
                'flagSponsor'       => $patient->sponsor->flag,
                'flagPatient'       => $patient->flag,
                'flagReason'        => $patient->flag_reason,
                'createdAt'         => (new Carbon($patient->created_at))->format('d/m/Y'),
                'createdBy'         => $patient->user->username,
                'active'            => $patient->is_active,
                'count'             => $patient->visits->count(),
                'patient'           => $patient->patientId()
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
        $query      = $this->patient::with(['user', 'sponsor.sponsorCategory', 'visits']);

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

    public function patientList($data)
    {
        if (! empty($data->fullId)){
            $searchTerm = '%' . addcslashes($data->fullId, '%_') . '%';
            return $this->patient::with('sponsor')
                        ->where('first_name', 'LIKE', $searchTerm)
                        ->orWhere('middle_name', 'LIKE', $searchTerm)
                        ->orWhere('last_name', 'LIKE', $searchTerm)
                        ->orWhere('phone', 'LIKE', $searchTerm )
                        ->orWhere('sex', 'LIKE', $searchTerm)
                        ->orWhere('date_of_birth', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm )
                        ->orderBy('created_at', 'asc')
                        ->pluck('first_name', 'middle_name', 'last_name', 'sponsor.name');
        }      
    }

    public function listTransformer()
    {
        return function (Patient $patient){
            return [
                'fullId'    => $patient->patientId(),
                'cardNo'    => $patient->card_no,
                'sponsor'   => $patient->sponsor->name,
            ];
        };
    }
}
