<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AntenatalRegisterationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                                => $this->id,
            'patient'                           => $this->patient->patientId(),
            'age'                               => $this->patient->age(),
            'sponsor'                           => $this->patient->sponsor->name.' - '.$this->patient->sponsor->category_name,
            'maritalStatus'                     => $this->marital_status,    
            'husbandName'                       => $this->husbands_name,    
            'husbandOccupation'                 => $this->husbands_occupation,
            'heartDisease'                      => $this->heart_disease,     
            'chestDisease'                      => $this->chest_disease,     
            'kidneyDisease'                     => $this->kidney_disease,    
            'bloodTransfusion'                  => $this->blood_transfusion, 
            'diabetes'                          => $this->diabetes,          
            'hypertension'                      => $this->hypertension,      
            'sickleCell'                        => $this->sickle_cell,       
            'others'                            => $this->others,            
            'multiplePregnacy'                  => $this->multiple_pregnancy,
            'lmp'                               => $this->lmp ? Carbon::parse($this->lmp)->format('Y-m-d') : '',
            'edd'                               => $this->edd ? Carbon::parse($this->edd)->format('Y-m-d') : '',               
            'ega'                               => $this->ega,               
            'previousPregnancies'               => $this->previous_pregnancies,
            'totalPregnancies'                  => $this->total_pregnancies,
            'noOfLivingChildren'                => $this->no_of_living_children,
            'bleeding'                          => $this->bleeding,
            'discharge'                         => $this->discharge,
            'urinarySymptoms'                   => $this->urinary_symptoms,
            'swellingOfAnkles'                  => $this->swelling_of_ankles,
            'otherSymptoms'                     => $this->other_symptoms,
            'generalConditionAnemia'            => $this->general_condition_anemia,
            'oedema'                            => $this->oedema,
            'anemia'                            => $this->anemia,
            'abdomen'                           => $this->abdomen,
            'specimen'                          => $this->specimen,
            'specimenCm'                        => $this->specimen_cm,
            'liver'                             => $this->liver,
            'liverCm'                           => $this->liver_cm,
            'virginalExamination'               => $this->virginal_examination,
            'otherAnomalies'                    => $this->other_anomalies,
            'height'                            => $this->height,
            'weight'                            => $this->weight,
            'bp'                                => $this->bp,
            'urine'                             => $this->urine,
            'breastNipples'                     => $this->breast_nipples,
            'hb'                                => $this->hb,
            'genotype'                          => $this->genotype,
            'vdrl'                              => $this->vdrl,
            'groupHr'                           => $this->group_hr,
            'rvst'                              => $this->rvst,
            'comments'                          => $this->comments,
            'instrRelatingToPueperium'          => $this->instr_rel_to_peuperium,
            'assessment'                        => $this->assessment,
            'hbGenotype'                        => $this->hb_genotype,
            'chestXray'                         => $this->chest_xray,
            'rhesus'                            => $this->rhesus,
            'antiMalAndSpecificTherapies'       => $this->ant_mal_and_specific_therapies,
            'pelvicAssessment'                  => $this->pelvic_assessment,
            'instructionsForDelivery'           => $this->instr_for_delivery,
        ];
    }
}
