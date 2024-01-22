<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\AntenatalRegisteration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

Class AntenatalRegisterationService
{
    public function __construct(private readonly AntenatalRegisteration $antenatalRegisteration)
    {
        
    }

    public function create(Request $data, User $user): AntenatalRegisteration
    {
       $antenatalRegisteration = $user->antenatalRegisterations()->create([
            'marital_status'        => $data->maritalStatus,
            'husbands_name'         => $data->husbandName,
            'husbands_occupation'   => $data->husbandOccupation,
            'heart_disease'         => $data->heartDisease,
            'chest_disease'         => $data->chestDisease,
            'kidney_disease'        => $data->kidneyDisease,
            'blood_transfusion'     => $data->bloodTransfusion,
            'diabetes'              => $data->diabetes,
            'hypertension'          => $data->hypertension,
            'sickle_cell'           => $data->sickleCell,
            'others'                => $data->others,
            'multiple_pregnancy'    => $data->multiplePregnacy,
            'lmp'                   => $data->lmp,
            'edd'                   => $data->edd,
            'ega'                   => $data->ega,
            'previous_pregnancies'  => $data->previousPregnancies,
            'total_pregnancies'     => $data->totalPregnancies,
            'no_of_living_children' => $data->noOfLivingChildren,
            'bleeding'              => $data->bleeding,
            'discharge'             => $data->discharge,
            'urinary_symptoms'      => $data->urinarySymptoms,
            'swelling_of_ankles'    => $data->swellingOfAnkles,
            'other_symptoms'            => $data->otherSymptoms,
            'general_condition_anemia'  => $data->generalConditionAnemia,
            'oedema'                    => $data->oedema,
            'anemia'                    => $data->anemia,
            'abdomen'                   => $data->abdomen,
            'specimen'                  => $data->specimen,
            'specimen_cm'               => $data->specimenCm,
            'liver'                     => $data->liver,
            'liver_cm'                  => $data->liverCm,
            'virginal_examination'      => $data->virginalExamination,
            'other_anomalies'           => $data->otherAnomalies,
            'height'                    => $data->height,
            'weight'                    => $data->weight,
            'bp'                        => $data->bp,
            'urine'                     => $data->urine,
            'breast_nipples'            => $data->breastNipples,
            'hb'                        => $data->hb,
            'genotype'                  => $data->genotype,
            'vdrl'                      => $data->vdrl,
            'group_hr'                  => $data->groupHr,
            'rvst'                      => $data->rvst,
            'comments'                  => $data->comments,
            'instr_rel_to_peuperium'    => $data->instrRelatingToPueperium,
            'assessment'                => $data->assessment,
            'hb_genotype'               => $data->hbGenotype,
            'chest_xray'                => $data->chestXray,
            'rhesus'                    => $data->rhesus,
            'ant_mal_and_specific_therapies' => $data->antiMalAndSpecificTherapies,
            'pelvic_assessment'         => $data->pelvicAssessment,
            'instr_for_delivery'        => $data->instructionsForDelivery,
            'patient_id'                => $data->patientId,
        ]);

        return $antenatalRegisteration;
    }

    public function update(Request $data, AntenatalRegisteration $antenatalRegisteration, User $user): AntenatalRegisteration
    {
        $antenatalRegisteration->update([
            'marital_status'        => $data->maritalStatus,
            'husbands_name'         => $data->husbandName,
            'husbands_occupation'   => $data->husbandOccupation,
            'heart_disease'         => $data->heartDisease,
            'chest_disease'         => $data->chestDisease,
            'kidney_disease'        => $data->kidneyDisease,
            'blood_transfusion'     => $data->bloodTransfusion,
            'diabetes'              => $data->diabetes,
            'hypertension'          => $data->hypertension,
            'sickle_cell'           => $data->sickleCell,
            'others'                => $data->others,
            'multiple_pregnancy'    => $data->multiplePregnacy,
            'lmp'                   => $data->lmp,
            'edd'                   => $data->edd,
            'ega'                   => $data->ega,
            'previous_pregnancies'  => $data->previousPregnancies,
            'total_pregnancies'     => $data->totalPregnancies,
            'no_of_living_children' => $data->noOfLivingChildren,
            'bleeding'              => $data->bleeding,
            'discharge'             => $data->discharge,
            'urinary_symptoms'      => $data->urinarySymptoms,
            'swelling_of_ankles'    => $data->swellingOfAnkles,
            'other_symptoms'            => $data->otherSymptoms,
            'general_condition_anemia'  => $data->generalConditionAnemia,
            'oedema'                    => $data->oedema,
            'anemia'                    => $data->anemia,
            'abdomen'                   => $data->abdomen,
            'specimen'                  => $data->specimen,
            'specimen_cm'               => $data->specimenCm,
            'liver'                     => $data->liver,
            'liver_cm'                  => $data->liverCm,
            'virginal_examination'      => $data->virginalExamination,
            'other_anomalies'           => $data->otherAnomalies,
            'height'                    => $data->height,
            'weight'                    => $data->weight,
            'bp'                        => $data->bp,
            'urine'                     => $data->urine,
            'breast_nipples'            => $data->breastNipples,
            'hb'                        => $data->hb,
            'genotype'                  => $data->genotype,
            'vdrl'                      => $data->vdrl,
            'group_hr'                  => $data->groupHr,
            'rvst'                      => $data->rvst,
            'comments'                  => $data->comments,
            'instr_rel_to_peuperium'    => $data->instrRelatingToPueperium,
            'assessment'                => $data->assessment,
            'hb_genotype'               => $data->hbGenotype,
            'chest_xray'                => $data->chestXray,
            'rhesus'                    => $data->rhesus,
            'ant_mal_and_specific_therapies' => $data->antiMalAndSpecificTherapies,
            'pelvic_assessment'         => $data->pelvicAssessment,
            'instr_for_delivery'        => $data->instructionsForDelivery,
            'user_id'                   => $user->id
        ]);

        return $antenatalRegisteration;
    }

    public function getantenatalRegisterations(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->antenatalRegisteration
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->antenatalRegisteration
                    ->where('consultation_id', $data->conId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getDeliveryNoteTransformer(): callable
    {
       return  function (AntenatalRegisteration $antenatalRegisteration) {
            return [
                'id'                => $antenatalRegisteration->id,
                'date'              => (new Carbon($antenatalRegisteration->date))->format('d/m/y'),
                'timeAdmitted'      => (new Carbon($antenatalRegisteration->time_of_admission))->format('d/m/y g:ia'),
                'timeDelivered'     => (new Carbon($antenatalRegisteration->time_of_delivery))->format('d/m/y g:ia'),
                'modeOfDelivery'    => $antenatalRegisteration->mode_of_delivery,
                'ebl'               => $antenatalRegisteration->ebl,
                'note'              => $antenatalRegisteration->note,
                'nurse'             => $antenatalRegisteration->user->username
            ];
         };
    }
}