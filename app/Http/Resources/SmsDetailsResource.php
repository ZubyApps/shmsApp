<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SmsDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $amountInWords  = new NumberFormatter();
        // $amountInWords->setTextAttribute(NumberFormatter::DEFAULT_RULESET, "%spellout-numbering-verbose");
        $outstanding    = $this->visit->total_hms_bill - $this->visit->discount - $this->visit->total_paid;
        return [
            'id'            => $this->id,
            'phone'         => $this->visit->patient->phone,
            'smsDetails'   => 'Dear ' . $this->visit->patient->first_name . ' ' . $this->visit->patient->last_name . ', pls be reminded of your hospital bill (N' .number_format($outstanding). ') incurred on ' . (new Carbon($this->visit->created_at))->format('d/m/Y') . '. Kindly send to Monie Point Nzube Okoye 5496896686 or visit the hospital',
        ];
    }
}
