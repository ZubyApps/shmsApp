<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicalReportResource extends JsonResource
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
            'type'          => $this->type,
            'doctor'        => $this->doctor,
            'designation'   => $this->designation,
            'requestedBy'   => $this->requested_by,
            'recipientsAddress'   => $this->recipients_address,
            'report'        => $this->report,
        ];
    }
}
