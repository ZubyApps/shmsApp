<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\LabourRecord;
use Illuminate\Http\Request;
use App\DataObjects\DataTableQueryParams;

use function Laravel\Prompts\info;

Class LabourRecordService
{
    public function __construct(private readonly LabourRecord $labourRecord)
    {
        
    }

    public function create(Request $data, User $user): LabourRecord
    {
       $labourRecord = $user->labourRecords()->create([
            // Demographic and pregnancy details
            'parity' => $data->parity,
            'no_of_living_children' => $data->noOfLivingChildren,
            'lmp' => $data->lmp,
            'edd' => $data->edd,
            'ega' => $data->ega,
            'onset' => $data->onset,
            'onset_hours' => $data->onsetHours,
            'spontaneous' => $data->spontaneous,
            'induced' => $data->induced,
            'amniotomy' => $data->amniotomy,
            'oxytocies' => $data->oxytocies,
            'cervical_dilation' => $data->cervicalDilation,
            'm_ruptured_at' => $data->mRupturedAt,
            'contractions_began' => $data->contractionsBegan,

            // Contraction quality
            'excellent' => $data->excellent,
            'good' => $data->good,
            'fair' => $data->fair,
            'poor' => $data->poor,

            // Physical measurements
            'fundal_height' => $data->fundalHeight,
            'multiple' => $data->multiple,
            'singleton' => $data->singleton,
            'lie' => $data->lie,
            'presentation' => $data->presentation,
            'position' => $data->position,
            'descent' => $data->descent,
            'foetal_heart_rate' => $data->foetalHeartRate,
            'vulva' => $data->vulva,
            'vagina' => $data->vagina,
            'cervix' => $data->cervix,
            'applied_to_pp' => $data->appliedToPp,
            'os' => $data->os,
            'membranes_ruptured' => $data->membranesRuptured,
            'membranes_intact' => $data->membranesIntact,
            'pp_at_o' => $data->ppAtO,
            'station_in' => $data->stationIn,
            'caput' => $data->caput,
            'moulding' => $data->moulding,
            'sp' => $data->sp,
            'sacral_curve' => $data->sacralCurve,
            'forecast' => $data->forecast,
            'ischial_spine' => $data->ischialSpine,
            'examiner' => $data->examiner,
            'designation' => $data->designation,
            'past_ob_history' => $data->pastObHistory,
            'antenatal_history' => $data->antenatalHistory,

            // Foreign keys
            'visit_id' => $data->visitId,
        ]);

        return $labourRecord;
    }

    public function update(Request $data, LabourRecord $labourRecord, User $user): LabourRecord
    {
        $labourRecord->update([
            // Demographic and pregnancy details
            'parity' => $data->parity,
            'no_of_living_children' => $data->noOfLivingChildren,
            'lmp' => $data->lmp,
            'edd' => $data->edd,
            'ega' => $data->ega,
            'onset' => $data->onset,
            'onset_hours' => $data->onsetHours,
            'spontaneous' => $data->spontaneous,
            'induced' => $data->induced,
            'amniotomy' => $data->amniotomy,
            'oxytocies' => $data->oxytocies,
            'cervical_dilation' => $data->cervicalDilation,
            'm_ruptured_at' => $data->mRupturedAt,
            'contractions_began' => $data->contractionsBegan,

            // Contraction quality
            'excellent' => $data->excellent,
            'good' => $data->good,
            'fair' => $data->fair,
            'poor' => $data->poor,

            // Physical measurements
            'fundal_height' => $data->fundalHeight,
            'multiple' => $data->multiple,
            'singleton' => $data->singleton,
            'lie' => $data->lie,
            'presentation' => $data->presentation,
            'position' => $data->position,
            'descent' => $data->descent,
            'foetal_heart_rate' => $data->foetalHeartRate,
            'vulva' => $data->vulva,
            'vagina' => $data->vagina,
            'cervix' => $data->cervix,
            'applied_to_pp' => $data->appliedToPp,
            'os' => $data->os,
            'membranes_ruptured' => $data->membranesRuptured,
            'membranes_intact' => $data->membranesIntact,
            'pp_at_o' => $data->ppAtO,
            'station_in' => $data->stationIn,
            'caput' => $data->caput,
            'moulding' => $data->moulding,
            'sp' => $data->sp,
            'sacral_curve' => $data->sacralCurve,
            'forecast' => $data->forecast,
            'ischial_spine' => $data->ischialSpine,
            'examiner' => $data->examiner,
            'designation' => $data->designation,
            'past_ob_history' => $data->pastObHistory,
            'antenatal_history' => $data->antenatalHistory,
        ]);

        return $labourRecord;
    }

    public function updateSummary(Request $data, LabourRecord $labourRecord, User $user): LabourRecord
    {
        $labourRecord->update([
            // Labor interventions
            'sol_amniotomy' => $data->solAmniotomy,
            'sol_a_indication' => $data->solAIndication,
            'sol_oxytocin' => $data->solOxytocin,
            'sol_o_indication' => $data->solOIndication,
            'sol_prostaglandins' => $data->solProstaglandins,
            'sol_p_indication' => $data->solPIndication,
            'd_of_labour' => $data->dOfLabour,
            'sol_spontaneous' => $data->solSpontaneous,
            'sol_assisted' => $data->solAssisted,
            'sol_forceps' => $data->solForceps,
            'extraction' => $data->extraction,
            'vacuum' => $data->vacuum,
            'internal_pod_version' => $data->internalPodVersion,
            'caesarean_section' => $data->caesareanSection,
            'destructive_operation' => $data->destructiveOperation,
            'd_o_specify' => $data->dOSpecify,
            'anaesthesia' => $data->anaesthesia,

            // Third stage of labor
            'p_spontaneous' => $data->pSpontaneous,
            'cct' => $data->cct,
            'manual_removal' => $data->manualRemoval,
            'complete' => $data->complete,
            'incomplete' => $data->incomplete,
            'placenta_weight' => $data->placentaWeight,
            'perineum_intact' => $data->perineumIntact,
            'first_degree_laceration' => $data->firstDegreeLaceration, // Note: Adjusted for clarity
            'second_degree_laceration' => $data->secondDegreeLaceration, // Note: Adjusted for clarity
            'third_degree_laceration' => $data->thirdDegreeLaceration, // Note: Adjusted for clarity
            'episiotomy' => $data->episiotomy,
            'repair_by' => $data->repairBy,
            'designation_repair' => $data->designationRepair,
            'no_of_skin_sutures' => $data->noOfSkinSutures,
            'blood_loss' => $data->bloodLoss,

            // Neonatal details
            'alive' => $data->alive,
            'sexes' => $data->sexes,
            'baby_weight' => $data->babyWeight,
            'apgar_score_1m' => $data->apgarScore1m,
            'apgar_score_5m' => $data->apgarScore5m,
            'fresh_still_birth' => $data->freshStillBirth,
            'macerated_still_birth' => $data->maceratedStillBirth,
            'immediate_neonatal_death' => $data->immediateNeonatalDeath,
            'malformation' => $data->malformation,

            // Maternal condition
            'mc_uterus' => $data->mcUterus,
            'mc_bladder' => $data->mcBladder,
            'mc_blood_pressure' => $data->mcBloodPressure,
            'mc_pulse_rate' => $data->mcPulseRate,
            'mc_temperature' => $data->mcTemperature,
            'mc_respiration' => $data->mcRespiration,
            'supervisor' => $data->supervisor,
            'blood_loss_treatment' => $data->bloodLossTreatment,
            'malformation_details' => $data->malformationDetails,
            'accoucheur' => $data->accoucheur,
            'summarized_by'    => $user->id
        ]);

        return $labourRecord;
    }
    
    public function deleteSummary(LabourRecord $labourRecord): LabourRecord
    {
        $labourRecord->update([
            // Labor interventions
            'sol_amniotomy' => false,
            'sol_a_indication' => null,
            'sol_oxytocin' => false,
            'sol_o_indication' => null,
            'sol_prostaglandins' => false,
            'sol_p_indication' => null,
            'd_of_labour' => null,
            'sol_spontaneous' => false,
            'sol_assisted' => false,
            'sol_forceps' => false,
            'extraction' => false,
            'vacuum' => false,
            'internal_pod_version' => false,
            'caesarean_section' => false,
            'destructive_operation' => false,
            'd_o_specify' => null,
            'anaesthesia' => false,

            // Third stage of labor
            'p_spontaneous' => false,
            'cct' => false,
            'manual_removal' => false,
            'complete' => false,
            'incomplete' => false,
            'placenta_weight' => null,
            'perineum_intact' => false,
            'first_degree_laceration' => false,
            'second_degree_laceration' => false,
            'third_degree_laceration' => false,
            'episiotomy' => false,
            'repair_by' => null,
            'designation_repair' => null,
            'no_of_skin_sutures' => null,
            'blood_loss' => null,

            // Neonatal details
            'alive' => false,
            'sexes' => null,
            'baby_weight' => null,
            'apgar_score_1m' => null,
            'apgar_score_5m' => null,
            'fresh_still_birth' => false,
            'macerated_still_birth' => false,
            'immediate_neonatal_death' => false,
            'malformation' => false,

            // Maternal condition
            'mc_uterus' => null,
            'mc_bladder' => null,
            'mc_blood_pressure' => null,
            'mc_pulse_rate' => null,
            'mc_temperature' => null,
            'mc_respiration' => null,
            'supervisor' => null,
            'blood_loss_treatment' => null,
            'malformation_details' => null,
            'accoucheur' => null,
            'summarized_by'    => null
        ]);

        return $labourRecord;
    }

    public function getLabourRecords(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return $this->labourRecord::with(['visit.patient', 'visit.sponsor', 'user', 'partographs'])
                    ->where('visit_id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLabourRecordTransformer(): callable
    {
       return  function (LabourRecord $labourRecord) {
            return [
                'id'                => $labourRecord->id,
                'date'              => $labourRecord->created_at->format('d/m/y g:ia'),
                'onset'             => $labourRecord->onset ? $labourRecord->onset->format('d/m/y g:ia') : '',
                'onsetHours'        => $labourRecord->onset_hours ? $labourRecord->onset_hours . 'hr(s)' : '',
                'contractionsBegan' => $labourRecord->contractions_began ? $labourRecord->contractions_began->format('d/m/y g:ia') : '',
                'examiner'          => $labourRecord->user->username,
                'cervicalDilation'  => $labourRecord->cervical_dilation,
                'patient'           => $labourRecord->visit->patient->patientId(),
                'age'               => $labourRecord->visit->patient->age(),
                'sponsorName'       => $labourRecord->visit->sponsor->name,
                'sponsorCategory'   => $labourRecord->visit->sponsor->category_name,
                'summarizedBy'      => $labourRecord->summarizedBy?->username,
                'nextCervixCheck'   => $labourRecord->partographs->where('parameter_type', 'cervical_dilation')->sortByDesc('recorded_at')->first()?->recorded_at ? 
                                        (new Carbon($labourRecord->partographs->where('parameter_type', 'cervical_dilation')->sortByDesc('recorded_at')->first()?->recorded_at))->addHours(4)->format('Y-m-d\TH:i:s') : '',
                
            ];
         };
    }

    public function inProgress()
    {
        return $this->labourRecord
                    ->where('summarized_by', null)
                    ->where('created_at', '>=', Carbon::now()->subDays(7))
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    // public function getLabourInProgressTransformer(): callable
    // {
    //    return  function (LabourRecord $labourRecord) {
    //         return [
    //             'id'                => $labourRecord->id,
    //             'date'              => $labourRecord->created_at->format('d/m/y g:ia'),
    //             'onset'             => $labourRecord->onset ? $labourRecord->onset->format('d/m/y g:ia') : '',
    //             'onsetHours'        => $labourRecord->onset_hours ? $labourRecord->onset_hours . 'hr(s)' : '',
    //             'membranesRuptured' => $labourRecord->m_ruptured_at ?  $labourRecord->m_ruptured_at->format('d/m/y g:ia') : '',
    //             'contractionsBegan' => $labourRecord->contractions_began ? $labourRecord->contractions_began->format('d/m/y g:ia') : '',
    //             'examiner'          => $labourRecord->user->username,
    //             'patient'           => $labourRecord->visit->patient->patientId(),
    //             'sponsorName'       => $labourRecord->visit->sponsor->name,
    //             'sponsorCategory'   => $labourRecord->visit->sponsor->category_name,
    //         ];
    //      };
    // }
}