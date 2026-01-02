<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientPreFormResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'patientType'      => $this->patient_type, 
            'address'          => $this->address, 
            'bloodGroup'       => $this->blood_group,
            'cardNumber'       => $this->card_no,
            'dateOfBirth'      => $this->date_of_birth,
            'email'            => $this->email,
            'ethnicGroup'      => $this->ethnic_group,
            'firstName'        => $this->first_name,
            'genotype'         => $this->genotype,
            'knownConditions'  => $this->known_conditions,
            'lastName'         => $this->last_name,
            'maritalStatus'    => $this->marital_status,
            'middleName'       => $this->middle_name,
            'nationality'      => $this->nationality,
            'nextOfKin'        => $this->next_of_kin,
            'nextOfKinPhone'   => $this->next_of_kin_phone,
            'nextOfKinRship'   => $this->next_of_kin_rship,
            'occupation'       => $this->occupation,
            'phone'            => $this->phone,
            'religion'         => $this->religion,
            'sex'              => $this->sex,
            'sponsorId'        => $this->sponsor->id,
            'sponsor'          => $this->sponsor->name,
            'sponsorCategory'  => $this->sponsor->sponsorCategory->id,
            'sponsorCategoryId'=> $this->sponsor->sponsorCategory->id,
            'staffId'          => $this->staff_id,
            'stateOrigin'      => $this->state_of_origin,
            'stateResidence'   => $this->state_of_residence,  
        ];

    }
}
