<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientService
{
    public function __construct(private readonly Patient $patient, private readonly HelperService $helperService)
    {
    }

    public function create(Request $data, User $user): Patient
    {
        return $user->patients()->create([
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
                "sponsor_id"            => $data->sponsor,
                "staff_Id"              => $data->staffId,
                "state_of_origin"       => $data->stateOrigin,
                "state_of_residence"    => $data->stateResidence,
        ]);
    }

    public function update(Request $data, Patient $patient, User $user): Patient
    {
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
                "sponsor_id"            => $data->sponsor,
                "staff_id"              => $data->staffId,
                "state_of_origin"       => $data->stateOrigin,
                "state_of_residence"    => $data->stateResidence,

        ]);

        return $patient;
    }

    public function updateKnownClinicalInfo(Request $data, Patient $patient, User $user): Patient
    {
        $data->bloodGroup ? $patient->update(["blood_group" => $data->bloodGroup]) : '';
        $data->genotype ? $patient->update(["genotype" => $data->genotype]): '';
        $data->knownConditions ? $patient->update(["known_conditions" => $patient->known_conditions . ', '.$data->knownConditions]) : '';
        
        return $patient;
    }

    public function getPaginatedPatients(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->patient
                        ->where('first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('phone', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('sex', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->patient
                    ->orderBy($orderBy, $orderDir)
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
                'createdAt'         => (new Carbon($patient->created_at))->format('d/m/Y'),
                'createdBy'         => $patient->user->username,
                'active'            => $patient->is_active,
                'count'             => $patient->visits()->count(),
                'patient'           => $patient->patientId()
            ];
         };
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
            ->selectRaw("SUM(CASE WHEN DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH) THEN 1 ELSE 0 END) AS zeroTo3m, SUM(CASE WHEN (DATE(date_of_birth) < DATE_SUB(CURDATE(), INTERVAL 3 MONTH) AND DATE(date_of_birth) >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)) THEN 1 ELSE 0 END) AS threeTo12m, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(1)->year} AND YEAR(date_of_birth) >= {$currentDate->subYears(5)->year}) THEN 1 ELSE 0 END) AS oneTo5yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(5)->year} AND YEAR(date_of_birth) > {$currentDate->subYears(13)->year}) THEN 1 ELSE 0 END) AS fiveto13yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(13)->year} AND YEAR(date_of_birth) >= {$currentDate->subYears(18)->year}) THEN 1 ELSE 0 END) AS thirteenTo18yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(18)->year} AND YEAR(date_of_birth) >= {$currentDate->subYears(48)->year}) THEN 1 ELSE 0 END) AS eighteenTo48yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(48)->year} AND YEAR(date_of_birth) >= {$currentDate->subYears(63)->year}) THEN 1 ELSE 0 END) AS fortyEightTo63yrs, SUM(CASE WHEN (YEAR(date_of_birth) < {$currentDate->subYears(63)->year}) THEN 1 ELSE 0 END) AS above63yrs , sex"
            )
            ->groupBy('sex')
            ->get()
            ->toArray();
    }

    public function getNewRegSummaryBySponsor(DataTableQueryParams $params, $data)
    {
        $current = Carbon::now();

        if (! empty($params->searchTerm)) {
            return DB::table('patients')
            ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(patients.id) as patientsCount')
            ->leftJoin('sponsors', 'patients.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
            ->whereMonth('patients.created_at', $current->month)
            ->whereYear('patients.created_at', $current->year)
            ->groupBy('sponsor')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->get()
            ->toArray();
        }

        return DB::table('patients')
            ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(patients.id) as patientsCount')
            ->leftJoin('sponsors', 'patients.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->whereMonth('patients.created_at', $current->month)
            ->whereYear('patients.created_at', $current->year)
            ->groupBy('sponsor')
            ->orderBy('sponsor')
            ->orderBy('patientsCount')
            ->get()
            ->toArray();
    }

    public function getPatientsBySponsor(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current = Carbon::now();

        if (! empty($params->searchTerm)) {
            return $this->patient
                        ->where('sponsor_id', $data->sponsorId)
                        ->where(function (Builder $query) use($params) {
                            $query->where('first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('phone', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sex', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $this->patient
                ->where('sponsor_id', $data->sponsorId)
                ->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }
}
