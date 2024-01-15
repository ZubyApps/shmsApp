<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeliveryNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'date'              => $this->date,
            'timeOfAdmission'   => $this->time_of_admission,
            'timeOfDelivery'    => $this->time_of_delivery,
            'apgarScore'        => $this->apgar_score,
            'birthWeight'       => $this->birth_weight,
            'modeOfDelivery'    => $this->mode_of_delivery,
            'lengthOfParity'    => $this->length_of_parity,
            'headCircumference' => $this->head_circumference,
            'sex'               => $this->sex,
            'ebl'               => $this->ebl,
        ];
    }
}
