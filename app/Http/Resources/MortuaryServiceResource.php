<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MortuaryServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                        => $this->id,
            "dateOfDeposit"             => $this->date_deposited,
            "deceasedName"              => $this->deceased_name,
            "sex"                       => $this->deceased_sex,
            "depositor"                 => $this->depositors_name,
            "depositorPhone"            => $this->depositors_phone,
            "depositorAddress"          => $this->depositors_address,
            "depositorRship"            => $this->depositors_relationship,
            "altCollectorName"          => $this->alt_collectors_name,
            "altCollectorAddress"       => $this->alt_collectors_address,
            "altCollectorPhone"         => $this->alt_collectors_phone,
            "altCollectorRship"         => $this->alt_collectors_relationship,
            "pickUpDate"                => $this->pickup_date,
        ];
    }
}
