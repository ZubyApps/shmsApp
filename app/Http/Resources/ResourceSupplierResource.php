<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ResourceSupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'company'   => $this->company,
            'person'    => $this->person,
            'phone'     => $this->phone,
            'email'     => $this->email,
            'address'   => $this->address
        ];
    }
}
