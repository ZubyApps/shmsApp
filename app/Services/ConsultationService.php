<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Patient;
use App\Models\User;
use App\Models\Consultation;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationService
{
    public function __construct(private readonly Consultation $consultation)
    {
    }

    public function create(Request $data, User $user): Consultation
    {
            $consultation = $user->consultations()->create([
                "visit_id"          => $data->visitId,
                "p_complain"        => $data->presentingComplain,
                "hop_complain"      => $data->historyOfPresentingComplain,
                "med_surg_history"  => $data->pastMedicalHistory,
                "specialist"        => $data->consultantSpecialist,
                "exam_findings"     => $data->examinationFindings,
                "obgyn_history"     => $data->obGynHistory,
                "icd11_diagnosis"   => $data->selectedDiagnosis,
                "ad_diagnosis"      => $data->additionalDiagnosis,
                "admission_status"  => $data->admit,
                "ward"              => $data->ward,
                "bed_no"            => $data->bedNumber,
                "lmp"               => $data->lmp,
                "edd"               => $data->edd,
                "ega"               => $data->ega,
                "fh_rate"           => $data->fetalHeartRate,
                "assessment"        => $data->assessment,
                "notes"             => $data->notes,
                "phys_plan"         => $data->plan,
                "complaint"         => $data->complaint,
                "ultrasound_report" => $data->ultrasoundReport,
                "p_position"        => $data->presentationPosition,
                "ho_fundus"         => $data->heightOfFundus,
                "roppt_brim"        => $data->relationOfPresentingPartToBrim,
            ]);  

            $consultation->visit()->update([
                'consulted'    => true,
            ]);
            
        return $consultation;
    }

    public function update(Request $data, Consultation $consultation, User $user): Consultation
    {
       $consultation->update([
                "p_complain"        => $data->presentingComplain,
                "hop_complain"      => $data->historyOfPresentingComplain,
                "med_surg_history"  => $data->pastMedicalHistory,
                "specialist"        => $data->consultantSpecialist,
                "exam_findings"     => $data->examinationFindings,
                "obgyn_history"     => $data->obyGynHistory,
                "icd11_diagnosis"   => $data->selectedDiagnosis,
                "ad_diagnosis"      => $data->additionalDiagnosis,
                "admission_status"  => $data->admit,
                "ward"              => $data->ward,
                "bed_no"            => $data->bedNumber,
                "lmp"               => $data->lmp,
                "edd"               => $data->edd,
                "ega"               => $data->ega,
                "fh_rate"           => $data->fetalHeartRate,
                "assessment"        => $data->assessment,
                "notes"             => $data->notes,
                "phys_plan"         => $data->plan,
                "complaint"         => $data->complaint,
                "untrasound_report" => $data->ultrasoundReport,
                "p_position"        => $data->presentationPosition,
                "ho_fundus"         => $data->heightOfFundus,
                "roppt_brim"        => $data->ropPartToBrim,
        ]);
        return $consultation;
    }

    public function getLoadTransformer(): callable
    {
       return  function (Consultation $consultation) {
            return [
                'id'                => $consultation->id,
                'visitId'           => $consultation->visit->id,
                'patientId'         => $consultation->visit->patient->id,
                'came'              => (new Carbon($consultation->visit->created_at))->format('d/m/Y g:ia'),
                'patient'           => $consultation->visit->patient->card_no.' ' .$consultation->visit->patient->first_name.' '. $consultation->visit->patient->middle_name.' '.$consultation->visit->patient->last_name,
                'doctor'            => $consultation->user->name,
                'diagnosis'         => $consultation->selectedDiagnosis,
                'sponsor'           => $consultation->visit->patient->sponsor->name,
                'status'            => $consultation->admissions_status,
                // 'age'               => (new Carbon($consultation->patient->date_of_birth))->age.'yrs',
                // 'patientType'       => $consultation->patient->patient_type,
                // 'status'            => $consultation->status
            ];
         };
    }

    public function initiateConsultation(Consultation $consultation, User $user) 
    {
        $consultation->update([
            'doctor'    =>  $user->username
        ]);
        return response()->json(["id" => $consultation->id]);
    }

    public function getConsultations(Request $request, Visit $visit)
    {
        return $this->consultation
                    ->where('visit_id', $visit->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
       
    }
}