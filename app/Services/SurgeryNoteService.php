<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\SurgeryNote;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

Class SurgeryNoteService
{
    public function __construct(private readonly SurgeryNote $surgeryNote)
    {
        
    }

    public function create(Request $data, User $user): SurgeryNote
    {
       $surgeryNote = $user->surgeryNotes()->create([
            'date'                  => $data->date,
            'type_of_operation'     => $data->typeOfOperation,
            'type_of_aneasthesia'   => $data->typeOfAneasthesia,
            'surgeon'               => $data->surgeon,
            'assistant_surgeon'     => $data->assistantSurgeon,
            'aneasthetist'          => $data->aneasthetist,
            'scrub_nurse'           => $data->scrubNurse,
            'surgical_procedure'    => $data->surgicalProcedure,
            'surgeons_notes'        => $data->surgeonsNotes,
            'aneasthetists_notes'   => $data->aneasthetistsNotes,
            'post_op_notes'         => $data->postOperationNotes,
            'pre_assessment'        => $data->preAssessment,
            'indication'            => $data->indication,
            'surgery'               => $data->surgery,
            'plan'                  => $data->plan,
            'pre_med'               => $data->preMed,
            'baseline'              => $data->baseline,
            'cannulation'           => $data->cannulation,
            'pre_loading'           => $data->preloading,
            'induction'             => $data->induction,
            'maintainance'          => $data->maintenance,
            'infusion'              => $data->infusion,
            'analgesics'            => $data->analgesics,
            'transfusion'           => $data->transfusion,
            'antibiotics'           => $data->antibiotics,
            'kos'                   => $data->kos,
            'eos'                   => $data->eos,
            'ebl'                   => $data->ebl,
            'immediate_post_op'     => $data->immediatePostOp,
            'tourniquet_time'       => $data->tourniquetTime,
            'tourniquet_out'        => $data->tourniquetOut,
            'baby_out'              => $data->babyOut,
            'female'                => $data->female,
            'male'                  => $data->male,
            'apgar_score'           => $data->apgarScore,
            'weight'                => $data->birthWeight,
            'cs_surgeon'            => $data->csSurgeon,
            'cs_anaesthetist'       => $data->csAneasthetist,
            'visit_id'              => $data->visitId
        ]);

        return $surgeryNote;
    }

    public function update(Request $data, SurgeryNote $surgeryNote, User $user): SurgeryNote
    {
        $surgeryNote->update([
            'date'                  => $data->date,
            'type_of_operation'     => $data->typeOfOperation,
            'type_of_aneasthesia'   => $data->typeOfAneasthesia,
            'surgeon'               => $data->surgeon,
            'assistant_surgeon'     => $data->assistantSurgeon,
            'aneasthetist'          => $data->aneasthetist,
            'scrub_nurse'           => $data->scrubNurse,
            'surgical_procedure'    => $data->surgicalProcedure,
            'surgeons_notes'        => $data->surgeonsNotes,
            'aneasthetists_notes'   => $data->aneasthetistsNotes,
            'post_op_notes'         => $data->postOperationNotes,
            'pre_assessment'        => $data->preAssessment,
            'indication'            => $data->indication,
            'surgery'               => $data->surgery,
            'plan'                  => $data->plan,
            'pre_med'               => $data->preMed,
            'baseline'              => $data->baseline,
            'cannulation'           => $data->cannulation,
            'pre_loading'           => $data->preloading,
            'induction'             => $data->induction,
            'maintainance'          => $data->maintenance,
            'infusion'              => $data->infusion,
            'analgesics'            => $data->analgesics,
            'transfusion'           => $data->transfusion,
            'antibiotics'           => $data->antibiotics,
            'kos'                   => $data->kos,
            'eos'                   => $data->eos,
            'ebl'                   => $data->ebl,
            'immediate_post_op'     => $data->immediatePostOp,
            'tourniquet_time'       => $data->tourniquetTime,
            'tourniquet_out'        => $data->tourniquetOut,
            'baby_out'              => $data->babyOut,
            'female'                => $data->female,
            'male'                  => $data->male,
            'apgar_score'           => $data->apgarScore,
            'weight'                => $data->birthWeight,
            'cs_surgeon'            => $data->csSurgeon,
            'cs_anaesthetist'       => $data->csAneasthetist,
            'user_id'               => $user->id
        ]);

        return $surgeryNote;
    }

    public function getSurgeryNotes(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->surgeryNote
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->where('visit_id', $data->visitId)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->surgeryNote
                    ->where('visit_id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getSurgeryNoteTransformer(): callable
    {
       return  function (SurgeryNote $surgeryNote) {
            return [
                'id'                => $surgeryNote->id,
                'date'              => (new Carbon($surgeryNote->date))->format('d/m/y'),
                'typeOfOperation'   => $surgeryNote->type_of_operation,
                'typeOfAneasthesia' => $surgeryNote->type_of_aneasthesia,
                'surgeon'           => $surgeryNote->surgeon,
                'surgeonsNotes'     => $surgeryNote->surgeons_notes,
                'postOpNotes'       => $surgeryNote->post_op_notes,
                'doctor'            => $surgeryNote->user->username,
                'closed'            => $surgeryNote->visit->closed
            ];
         };
    }
}