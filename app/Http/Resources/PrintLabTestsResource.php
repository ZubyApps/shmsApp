<?php

namespace App\Http\Resources;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PrintLabTestsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * 
     *
     */
    public function toArray(Request $request): array
    {
        return [
            'test'   => Resource::where('id', $this->resource_id)->first()->name,
            'result' => $this->result,
        ];
    }
}
