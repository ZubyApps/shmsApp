<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DesignationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->designation->id,
            'fullName'      => $this->nameInFull(),
            'designation'   => $this->designation->designation,
            'accessLevel'   => $this->designation->access_level,
        ];
    }
}
