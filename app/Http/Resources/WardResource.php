<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'shortName'    => $this->short_name,
            'longName'     => $this->long_name,
            'bedNumber'    => $this->bed_number,
            'description'  => $this->description,
            'bill'         => $this->bill,
            'flag'         => $this->flag,
            'flagReason'   => $this->reason,
        ];
    }
}
