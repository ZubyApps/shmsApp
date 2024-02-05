<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'firstName1'             => $this->firstname,
            'middleName1'            => $this->middlename,
            'lastName1'              => $this->lastname,
            'username1'              => $this->username,
            'phoneNumber1'           => $this->phone_number,
            'email1'                 => $this->email,
            'address1'               => $this->address,
            'highestQualification1'  => $this->highest_qualification,
            'dateOfBirth1'           => $this->date_of_birth,
            'sex1'                   => $this->sex,
            'maritalStatus1'         => $this->marital_status,
            'specialNote1'           => $this->special_note,
            'stateOfOrigin1'         => $this->state_of_origin,
            'nextOfKin1'             => $this->next_of_kin,
            'nextOfKinRship1'        => $this->next_of_kin_rship,
            'nextOfKinPhone1'        => $this->next_of_kin_phone,
            'dateOfEmployment1'      => $this->date_of_employment,
            'dateOfExit1'            => $this->date_of_exit,
        ];
    }
}
