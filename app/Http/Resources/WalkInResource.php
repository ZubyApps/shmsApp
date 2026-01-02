<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WalkInResource extends JsonResource
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
            'firstName'        => $this->first_name,
            'middleName'       => $this->middle_name,
            'lastName'         => $this->last_name,
            'dateOfBirth'      => $this->date_of_birth,
            'phone'            => $this->phone,
            'address'          => $this->address, 
            'sex'              => $this->sex,
            'occupation'       => $this->occupation,
            'prevXray'         => $this->prev_xray,
            'dateOfXray'       => $this->date_of_xray,
            'clinicalDiagnosis'=> $this->clinical_diagnosis,
            'clinicalFeatures' => $this->clinical_features,
        ];
    }
}
