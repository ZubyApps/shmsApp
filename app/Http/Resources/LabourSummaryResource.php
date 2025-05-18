<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LabourSummaryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            // Labor interventions
            'solAmniotomy' => $this->sol_amniotomy,
            'solAIndication' => $this->sol_a_indication,
            'solOxytocin' => $this->sol_oxytocin,
            'solOIndication' => $this->sol_o_indication,
            'solProstaglandins' => $this->sol_prostaglandins,
            'solPIndication' => $this->sol_p_indication,
            'dOfLabour' => $this->d_of_labour ? $this->d_of_labour->toDateString() : null,
            'solSpontaneous' => $this->sol_spontaneous,
            'solAssisted' => $this->sol_assisted,
            'solForceps' => $this->sol_forceps,
            'extraction' => $this->extraction,
            'vacuum' => $this->vacuum,
            'internalPodVersion' => $this->internal_pod_version,
            'caesareanSection' => $this->caesarean_section,
            'destructiveOperation' => $this->destructive_operation,
            'dOSpecify' => $this->d_o_specify,
            'anaesthesia' => $this->anaesthesia,

            // Third stage of labor
            'pSpontaneous' => $this->p_spontaneous,
            'cct' => $this->cct,
            'manualRemoval' => $this->manual_removal,
            'complete' => $this->complete,
            'incomplete' => $this->incomplete,
            'placentaWeight' => $this->placenta_weight,
            'perineumIntact' => $this->perineum_intact,
            'firstDegreeLaceration' => $this->first_degree_laceration,
            'secondDegreeLaceration' => $this->second_degree_laceration,
            'thirdDegreeLaceration' => $this->third_degree_laceration,
            'episiotomy' => $this->episiotomy,
            'repairBy' => $this->repair_by,
            'designationRepair' => $this->designation_repair,
            'noOfSkinSutures' => $this->no_of_skin_sutures,
            'bloodLoss' => $this->blood_loss,

            // Neonatal details
            'alive' => $this->alive,
            'sexes' => $this->sexes,
            'babyWeight' => $this->baby_weight,
            'apgarScore1m' => $this->apgar_score_1m,
            'apgarScore5m' => $this->apgar_score_5m,
            'freshStillBirth' => $this->fresh_still_birth,
            'maceratedStillBirth' => $this->macerated_still_birth,
            'immediateNeonatalDeath' => $this->immediate_neonatal_death,
            'malformation' => $this->malformation,

            // Maternal condition
            'mcUterus' => $this->mc_uterus,
            'mcBladder' => $this->mc_bladder,
            'mcBloodPressure' => $this->mc_blood_pressure,
            'mcPulseRate' => $this->mc_pulse_rate,
            'mcTemperature' => $this->mc_temperature,
            'mcRespiration' => $this->mc_respiration,
            'bloodLossTreatment' => $this->blood_loss_treatment,
            'malformationDetails' => $this->malformation_details,
            'supervisor' => $this->supervisor ?? $this->summarizedBy?->username,
            'accoucheur' => $this->accoucheur,
        ];
    }
}
