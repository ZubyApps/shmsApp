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
            "patientId"     => $this->patient->card_no . ' ' . $this->patient->first_name . ' ' . $this->patient->middle_name . ' ' . $this->patient->last_name,
            "sponsorName"   => $this->sponsor->name,
            "age"           => str_replace(['a', 'g', 'o'], '', (new Carbon($this->patient->date_of_birth))->diffForHumans(['other' => null, 'parts' => 2, 'short' => true]), ),
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
