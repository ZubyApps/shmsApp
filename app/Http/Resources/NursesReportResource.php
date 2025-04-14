<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NursesReportResource extends JsonResource
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
            'shift'        => $this->shift,
            'report'        => $this->report,
            'patient'       => $this->patient->patientId(),
            'sponsorName'   => $this->visit->sponsor->name
        ];
    }
}
