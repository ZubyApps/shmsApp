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
        $isNhis = $this->visit->sponsor->category_name == 'NHIS';
        $visit  = $this->visit;
        $outstanding    = ($isNhis ? $visit->total_nhis_bill : $visit->total_hms_bill) - $visit->discount - $visit->total_paid;
        return [
            'id'            => $this->id,
            'phone'         => $visit->patient->phone,
            'smsDetails'   => 'Dear ' . $visit->patient->first_name . ' ' . $visit->patient->last_name . ', pls be reminded of your hospital bill (N' .number_format($outstanding). ') incurred on ' . (new Carbon($visit->created_at))->format('d/m/Y') . '. Kindly send to Monie Point Nzube Okoye 5496896686 or visit the hospital',
        ];
    }
}
