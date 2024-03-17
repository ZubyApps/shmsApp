<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientHistoryBioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->id,
            'ancRegId'      => $this->antenatalRegisteration?->id,
            "patientId"     => $this->patientId(),
            'cardNo'        => $this->card_no,
            "patientType"   => $this->patient_type,
            "sponsorName"   => $this->sponsor->name,
            "age"           => $this->age(),
            "sex"           => $this->sex,
            "address"       => $this->address,
            "maritalStatus" => $this->marital_status,
            "phone"         => $this->phone,
            "ethnicGroup"   => $this->ethnic_group,
            "religion"      => $this->religion,
            "staffId"       => $this->staff_id,
            "bloodGroup"    => $this->blood_group,
            "genotype"      => $this->genotype,
            "knownConditions"=> $this->known_conditions
        ];
    }
}
