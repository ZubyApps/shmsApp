<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DisplayMedicalReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'type'              => $this->type,
            'doctor'            => $this->doctor,
            'designation'       => $this->designation,
            'recipientsAddress'   => $this->recipients_address,
            'report'            => $this->report,
            'patientsFullName'  => $this->patient->fullName(),
            'patientsInfo'      => strtoupper($this->patient->fullName().', '.$this->patient->age().', '.$this->patient->sex),
            // 'age'               => $this->patient->age(),
            // 'sex'               => $this->patient->sex,
        ];
    }
}
