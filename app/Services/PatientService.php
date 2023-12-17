<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientService
{
    public function __construct(private readonly Patient $patient)
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
        $patient->update([
                "blood_group"           => $data->bloodGroup,
                "genotype"              => $data->genotype,
                "known_conditions"      => $data->knownConditions,
            ]);
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
                'name'              => $patient->first_name.' '. $patient->middle_name.' '.$patient->last_name,
                'phone'             => $patient->phone,
                'sex'               => $patient->sex,
                'age'               => $patient->age(),
                'sponsor'           => $patient->sponsor->name,
                'category'          => $patient->sponsor->sponsorCategory->name,
                'createdAt'         => (new Carbon($patient->created_at))->format('d/m/Y'),
                'active'            => $patient->is_active,
                'count'             => $patient->visits()->count(),
                'patient'           => $patient->patientId()
            ];
         };
    }
}
