<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        return [
            "visitId"                       => $this->visit_id,
            "presentingComplain"            => $this->p_complain ?? '',
            "historyOfPresentingComplain"   => $this->hop_complain ?? '',
            "pastMedicalHistory"            => $this->med_surg_history ?? '',
            "consultantSpecialist"          => $this->specialist ?? '',
            "examinationFindings"           => $this->exam_findings ?? '',
            "obyGynHistory"                 => $this->obgyn_history ?? '',
            "selectedDiagnosis"             => $this->icd11_diagnosis ?? '',
            "additionalDiagnosis"           => $this->ad_diagnosis ?? '',
            "status"                        => $this->admission_status ?? '',
            "ward"                          => $this->ward ?? '',
            "bedNumber"                     => $this->bed_no ?? '',
            "lmp"                           => $this->lmp ?? '',
            "edd"                           => $this->edd ?? '',
            "ega"                           => $this->ega ?? '',
            "fetalHeartRate"                => $this->fh_rate ?? '',
            "assessment"                    => $this->assessment ?? '',
            "notes"                          => $this->notes ?? '',
            "plan"                          => $this->phys_plan ?? '',
            "complaint"                     => $this->complaint ?? '',
            "ultrasoundReport"              => $this->ultrasound_report ?? '',
            "presentationPosition"          => $this->p_position ?? '',
            "heightOfFundus"                => $this->ho_fundus ?? '',
            "relationOfPresentingPartToBrim"=> $this->roppt_brim ?? '',
            "remarks"                       => $this->remarks ?? '',
            "doctor"                        => $this->user->username ?? '',
        ];
    }
}
