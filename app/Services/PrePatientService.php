<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Patient;
use App\Models\PatientPreForm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PrePatientService
{
    public function __construct(
        private readonly Patient $patient, 
        private readonly HelperService $helperService, 
        private readonly PatientPreForm $patientPreForm
        )
    {
    }

    public function updateForm(Request $data, PatientPreForm $patientPreForm): PatientPreForm
    {
        return DB::transaction(function () use($data, $patientPreForm){
            $patientPreForm->update([
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
                    "sponsor_category"      => $data->sponsorCategory,
                    "staff_Id"              => $data->staffId,
                    "state_of_origin"       => $data->stateOrigin,
                    "state_of_residence"    => $data->stateResidence,
            ]);
    
            return $patientPreForm;
        });
    }

    public function getPaginatedPrePatients(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->patientPreForm::with(['sponsor', 'sponsor.sponsorCategory', 'user']);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where('first_name', 'LIKE', $searchTerm)
                        ->orWhere('middle_name', 'LIKE', $searchTerm)
                        ->orWhere('last_name', 'LIKE', $searchTerm)
                        ->orWhere('card_no', 'LIKE', $searchTerm)
                        ->orWhere('phone', 'LIKE', $searchTerm)
                        ->orWhere('sex', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('date_of_birth', 'LIKE', addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor.sponsorCategory', 'name', 'LIKE', $searchTerm)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (PatientPreForm $patientPreForm) {
            return [
                'id'                => $patientPreForm->id,
                'card'              => $patientPreForm->card_no,
                'patient'           => $patientPreForm->fullName(),
                'phone'             => $patientPreForm->phone,
                'sex'               => $patientPreForm->sex,
                'age'               => $this->helperService->twoPartDiffInTimePast($patientPreForm->date_of_birth),
                'sponsor'           => $patientPreForm->sponsor->name,
                'category'          => $patientPreForm->sponsor->sponsorCategory->name,
                'flagSponsor'       => $patientPreForm->sponsor->flag,
                'flagPatient'       => $patientPreForm->flag,
                'flagReason'        => $patientPreForm->flag_reason,
                'createdAt'         => (new Carbon($patientPreForm->created_at))->format('d/m/Y'),
                'createdBy'         => $patientPreForm->user->username,
                'active'            => $patientPreForm->is_active,
            ];
         };
    }
}
