<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabourRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // Demographic and pregnancy details
            'parity' => $this->parity,
            'noOfLivingChildren' => $this->no_of_living_children,
            'lmp' => $this->lmp ? $this->lmp->toDateString() : null,
            'edd' => $this->edd ? $this->edd->toDateString() : null,
            'ega' => $this->ega,
            'onset' => $this->onset ? $this->onset->toDateTimeString() : null,
            'onsetHours' => $this->onset_hours,
            'spontaneous' => $this->spontaneous,
            'induced' => $this->induced,
            'amniotomy' => $this->amniotomy,
            'oxytocies' => $this->oxytocies,
            'mRupturedAt' => $this->m_ruptured_at ? $this->m_ruptured_at->toDateTimeString() : null,
            'contractionsBegan' => $this->contractions_began ? $this->contractions_began->toDateTimeString() : null,

            // Contraction quality
            'excellent' => $this->excellent,
            'good' => $this->good,
            'fair' => $this->fair,
            'poor' => $this->poor,

            // Physical measurements
            'fundalHeight' => $this->fundal_height,
            'multiple' => $this->multiple,
            'singleton' => $this->singleton,
            'lie' => $this->lie,
            'presentation' => $this->presentation,
            'position' => $this->position,
            'descent' => $this->descent,
            'foetalHeartRate' => $this->foetal_heart_rate,
            'vulva' => $this->vulva,
            'vagina' => $this->vagina,
            'cervix' => $this->cervix,
            'appliedToPp' => $this->applied_to_pp,
            'os' => $this->os,
            'membranesRuptured' => $this->membranes_ruptured,
            'membranesIntact' => $this->membranes_intact,
            'ppAtO' => $this->pp_at_o,
            'stationIn' => $this->station_in,
            'caput' => $this->caput,
            'moulding' => $this->moulding,
            'sp' => $this->sp,
            'sacralCurve' => $this->sacral_curve,
            'forecast' => $this->forecast,
            'ischialSpine' => $this->ischial_spine,
            'designation' => $this->designation,
            'pastObHistory' => $this->past_ob_history,
            'antenatalHistory' => $this->antenatal_history,
            'examiner' => $this->examiner ?? $this->user->username,
        ];
    }
}
