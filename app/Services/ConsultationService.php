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
                "visit_id"                  => $data->visitId,
                "p_complain"                => $data->presentingComplain,
                "hop_complain"              => $data->historyOfPresentingComplain,
                "med_surg_history"          => $data->pastMedicalHistory,
                "specialist"                => $data->consultantSpecialist,
                "exam_findings"             => $data->examinationFindings,
                "obgyn_history"             => $data->obGynHistory,
                "icd11_diagnosis"           => $data->selectedDiagnosis,
                "ad_diagnosis"              => $data->additionalDiagnosis,
                "admission_status"          => $data->admit,
                "ward"                      => $data->ward,
                "bed_no"                    => $data->bedNumber,
                "lmp"                       => $data->lmp,
                "edd"                       => $data->edd,
                "ega"                       => $data->ega,
                "fh_rate"                   => $data->fetalHeartRate,
                "assessment"                => $data->assessment,
                "notes"                     => $data->notes,
                "phys_plan"                 => $data->plan,
                "complaint"                 => $data->complaint,
                "ultrasound_report"         => $data->ultrasoundReport,
                "p_position"                => $data->presentationPosition,
                "ho_fundus"                 => $data->heightOfFundus,
                "roppt_brim"                => $data->relationOfPresentingPartToBrim,
                "specialist_consultaion"    => $data->specialConsultation
            ]);  

            $consultation->visit()->update([
                'consulted'    => new Carbon(),
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

    public function getConsultations(Request $request, Visit $visit)
    {
        return $this->consultation
                    ->where('visit_id', $visit->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
       
    }
}