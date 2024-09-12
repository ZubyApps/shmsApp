<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use NumberFormatter;

class SmsDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $amountInWords  = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $amountInWords->setTextAttribute(NumberFormatter::DEFAULT_RULESET, "%spellout-numbering-verbose");
        $outstanding    = $this->visit->total_hms_bill - $this->visit->total_paid;
        return [
            'id'            => $this->id,
            'phone'         => $this->visit->patient->phone,
            'smsDetails'   => 'Dear ' . $this->visit->patient->first_name . ', kindly clear your hospital bill (' . ucwords($amountInWords->format($outstanding)) . ' Naira) incured on ' . (new Carbon($this->visit->created_at))->format('d/m/Y') . '. Send to Monie/Point Sandra hospita (Nzube Okoye) 549/689/6686 or visit the hospital',
        ];
    }
}
