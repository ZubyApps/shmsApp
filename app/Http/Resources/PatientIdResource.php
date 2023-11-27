<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientIdResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'patientId'    => $this->card_no . ' ' . $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name."'s"
        ];
    }
}
