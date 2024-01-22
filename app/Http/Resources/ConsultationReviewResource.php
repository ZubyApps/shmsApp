<?php

namespace App\Http\Resources;

use Carbon\Carbon;
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
        return [
            "id"                            => $this->id,
            "visitId"                       => $this->visit_id,
            "presentingComplain"            => $this->p_complain ?? '',
            "historyOfPresentingComplain"   => $this->hop_complain ?? '',
            "pastMedicalHistory"            => $this->med_surg_history ?? '',
            "consultantSpecialist"          => $this->specialist ?? '',
            "examinationFindings"           => $this->exam_findings ?? '',
            "obGynHistory"                  => $this->obgyn_history ?? '',
            "selectedDiagnosis"             => $this->icd11_diagnosis ?? '',
            "provisionalDiagnosis"          => $this->provisional_diagnosis ?? '',
            "status"                        => $this->admission_status ?? '',
            "ward"                          => $this->ward ?? '',
            "bedNumber"                     => $this->bed_no ?? '',
            "lmp"                           => $this->lmp ? Carbon::parse($this->lmp)->format('d/M/Y') : '',
            "edd"                           => $this->edd ? Carbon::parse($this->edd)->format('d/M/Y') : '',
            "ega"                           => $this->ega ?? '',
            "fetalHeartRate"                => $this->fh_rate ?? '',
            "assessment"                    => $this->assessment ?? '',
            "notes"                         => $this->notes ?? '',
            "plan"                          => $this->phys_plan ?? '',
            "complaint"                     => $this->complaint ?? '',
            "ultrasoundReport"              => $this->ultrasound_report ?? '',
            "presentationAndPosition"       => $this->p_position ?? '',
            "heightOfFundus"                => $this->ho_fundus ?? '',
            "relationOfPresentingPartToBrim"=> $this->roppt_brim ?? '',
            "remarks"                       => $this->remarks ?? '',
            "doctor"                        => $this->user->username ?? '',
            "specialistFlag"                => $this->specialist_consultation ?? '',
            "deliveryNotes"                 => $this->deliveryNotes->count()
        ];
    }
}
