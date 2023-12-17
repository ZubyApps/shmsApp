<?php

namespace App\Http\Resources;

use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientBioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"            => $this->patient->id,
            "visitId"       => $this->id,
            "patientId"     => $this->patient->patientId(),
            "sponsorName"   => $this->sponsor->name,
            "age"           => $this->patient->age(),
            "sex"           => $this->patient->sex,
            "maritalStatus" => $this->patient->marital_status,
            "phone"         => $this->patient->phone,
            "ethnicGroup"   => $this->patient->ethnic_group,
            "religion"      => $this->patient->religion,
            "staffId"       => $this->patient->staff_id,
            "bloodGroup"    => $this->patient->blood_group,
            "genotype"      => $this->patient->genotype,
            "knownConditions"=> $this->patient->known_conditions
        ];
    }
}
