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
            'firstName'             => $this->firstname,
            'middleName'            => $this->middlename,
            'lastName'              => $this->lastname,
            'username'              => $this->username,
            'phoneNumber'           => $this->phone_number,
            'email'                 => $this->email,
            'address'               => $this->address,
            'highestQualification'  => $this->highest_qualification,
            'dateOfBirth'           => $this->date_Of_Birth,
            'sex'                   => $this->sex,
            'maritalStatus'         => $this->marital_status,
            'stateOfOrigin'         => $this->state_of_origin,
            'nextOfKin'             => $this->next_of_kin,
            'nextOfKinRship'        => $this->next_of_kin_rship,
            'nextOfKinPhone'        => $this->next_of_kin_phone,
            'dateOfEmployment'      => $this->date_of_employment,
            'dateOfExit'            => $this->date_of_exit,
        ];
    }
}
