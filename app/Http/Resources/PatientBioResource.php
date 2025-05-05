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
            'ancRegId'      => $this->patient->antenatalRegisteration?->id,
            "patientId"     => $this->patient->patientId(),
            "visitType"     => $this->visit_type,
            "sponsorName"   => $this->sponsor->name . ' - ' . $this->sponsor->category_name,
            "age"           => $this->patient->age(),
            "sex"           => $this->patient->sex,
            "address"       => $this->patient->address,
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
