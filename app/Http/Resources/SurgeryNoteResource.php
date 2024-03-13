<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SurgeryNoteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'date'                  => $this->date,
            'typeOfOperation'       => $this->type_of_operation,
            'typeOfAneasthesia'     => $this->type_of_aneasthesia,
            'surgeon'               => $this->surgeon,
            'assistantSurgeon'      => $this->assistant_surgeon,
            'aneasthetist'          => $this->aneasthetist,
            'scrubNurse'            => $this->scrub_nurse,
            'surgicalProcedure'     => $this->surgical_procedure,
            'surgeonsNotes'         => $this->surgeons_notes,
            'aneasthetistsNotes'    => $this->aneasthetists_notes,
            'postOperationNotes'    => $this->post_op_notes,
            'preAssessment'         => $this->pre_assessment,
            'indication'            => $this->indication,
            'surgery'               => $this->surgery,
            'plan'                  => $this->plan,
            'preMed'                => $this->pre_med,
            'baseline'              => $this->baseline,
            'cannulation'           => $this->cannulation,
            'preloading'            => $this->pre_loading,
            'induction'             => $this->induction,
            'maintenance'           => $this->maintenance,
            'infusion'              => $this->infusion,
            'analgesics'            => $this->analgesics,
            'transfusion'           => $this->transfusion,
            'antibiotics'           => $this->antibiotics,
            'kos'                   => $this->kos,
            'eos'                   => $this->eos,
            'ebl'                   => $this->ebl,
            'immediatePostOp'       => $this->immediate_post_op,
            'tourniquetTime'        => $this->tourniquet_time,
            'tourniquetOut'         => $this->tourniquet_out,
            'babyOut'               => $this->baby_out,
            'female'                => $this->female,
            'male'                  => $this->male,
            'apgarScore'            => $this->apgar_score,
            'birthWeight'           => $this->weight,
            'csSurgeon'             => $this->cs_ssurgeon,
            'csAneasthetist'        => $this->cs_anaesthetist,
        ];
    }
}
