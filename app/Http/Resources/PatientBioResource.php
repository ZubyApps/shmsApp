<?php

namespace App\Http\Resources;

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
        //var_dump($this[1]->id); exit;
        return [
            "id"            => $this[0]->id,
            "visitId"       => $this[1]->id,
            "patientId"     => $this[0]->card_no . ' ' . $this[0]->first_name . ' ' . $this[0]->middle_name . ' ' . $this[0]->last_name,
            "sponsorName"   => $this[0]->sponsor->name,
            "age"           => (new Carbon($this[0]->date_of_birth))->age,
            "sex"           => $this[0]->sex,
            "maritalStatus" => $this[0]->marital_status,
            "phone"         => $this[0]->phone,
            "ethnicGroup"   => $this[0]->ethnic_group,
            "religion"      => $this[0]->religion,
            "staffId"       => $this[0]->staff_id,
            "bloodGroup"    => $this[0]->blood_group,
            "genotype"      => $this[0]->genotype,
            "knownConditions"=> $this[0]->known_conditions
        ];
    }
}
