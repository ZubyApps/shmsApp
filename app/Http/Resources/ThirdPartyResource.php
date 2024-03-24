<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ThirdPartyResource extends JsonResource
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
            'fullName'          => $this->full_name,
            'shortName'         => $this->short_name,
            'phone'             => $this->phone,
            'address'           => $this->address,
            'email'             => $this->email,
            'comment'           => $this->comment,
        ];
    }
}
