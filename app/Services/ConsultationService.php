<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\User;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Visit;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationService
{
    public function __construct(
        private readonly Consultation $consultation, 
        private readonly Visit $visit, 
        private readonly Ward $ward
        )
    {
    }

    public function create(Request $data, User $user): Consultation
    {

        return DB::transaction(function () use ($data, $user) {

            $consultation = $user->consultations()->create([
                "visit_id"                  => $data->visitId,
                "p_complain"                => $data->presentingComplain,
                "hop_complain"              => $data->historyOfPresentingComplain,
                "med_surg_history"          => $data->pastMedicalHistory,
                "specialist"                => $data->consultantSpecialist,
                "exam_findings"             => $data->examinationFindings,
                "paediatric_history"        => $data->paediatricHistory,
                "obgyn_history"             => $data->obGynHistory,
                "icd11_diagnosis"           => $data->selectedDiagnosis,
                "provisional_diagnosis"     => $data->provisionalDiagnosis,
                "admission_status"          => $data->admit,
                "lmp"                       => $data->lmp,
                "edd"                       => $data->edd,
                "ega"                       => $data->ega,
                "fh_rate"                   => $data->fetalHeartRate,
                "assessment"                => $data->assessment,
                "history_of_care"           => $data->historyOfCare,
                "notes"                     => $data->notes,
                "remarks"                   => $data->remarks,
                "phys_plan"                 => $data->plan,
                "complaint"                 => $data->complaint,
                "p_position"                => $data->presentationAndPosition,
                "ho_fundus"                 => $data->heightOfFundus,
                "roppt_brim"                => $data->relationOfPresentingPartToBrim,
                "specialist_consultation"   => $data->specialConsultation
            ]);  

            $visit = $consultation->visit;

            $this->determineWard($visit, $consultation, $data);
     
        return $consultation;
        });
    }

    public function update(Request $data, Consultation $consultation, User $user)
    {
        return DB::transaction(function () use ($data, $consultation, $user) {
            
            $consultation->update([
                "p_complain"                => $data->presentingComplain,
                "hop_complain"              => $data->historyOfPresentingComplain,
                "med_surg_history"          => $data->pastMedicalHistory,
                "specialist"                => $data->consultantSpecialist,
                "exam_findings"             => $data->examinationFindings,
                "paediatric_history"        => $data->paediatricHistory,
                "obgyn_history"             => $data->obGynHistory,
                "icd11_diagnosis"           => $data->selectedDiagnosis,
                "provisional_diagnosis"     => $data->provisionalDiagnosis,
                "admission_status"          => $data->admit,
                "lmp"                       => $data->lmp,
                "edd"                       => $data->edd,
                "ega"                       => $data->ega,
                "fh_rate"                   => $data->fetalHeartRate,
                "assessment"                => $data->assessment,
                "history_of_care"           => $data->historyOfCare,
                "notes"                     => $data->notes,
                "remarks"                   => $data->remarks,
                "phys_plan"                 => $data->plan,
                "complaint"                 => $data->complaint,
                "p_position"                => $data->presentationAndPosition,
                "ho_fundus"                 => $data->heightOfFundus,
                "roppt_brim"                => $data->relationOfPresentingPartToBrim,
                "specialist_consultation"   => $data->specialConsultation,
            ]);

            $visit = $consultation->visit;

            $this->determineWard($visit, $consultation, $data, $user);

            return $consultation;
        });
    }

    public function updateAdmissionStatus(Consultation $consultation, Request $data, User $user)
    {
        return DB::transaction(function () use ($data, $consultation, $user) {

            $visit = $consultation->visit;

            return $this->determineWard($visit, $consultation, $data, $user);
        });
    }

    public function getConsultations(Visit $visit)
    {
        return DB::transaction(function () use ($visit) {

            return $this->consultation
                        ->where('visit_id', $visit->id)
                        ->orderBy('created_at', 'asc')
                        ->get();
        });
    }

    public function getVisitsAndConsultations(Patient $patient)
    {
        return $this->visit
                    ->where('patient_id', $patient->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }

    public function determineWard($visit, $consultation, $data, ?User $user = null)
    {
        $wardModel = fn($wardId)=>$this->ward::find($wardId);

        if ($ward = $wardModel($visit->ward_id)){
            $ward->visit_id == $visit->id ? $ward->update(['visit_id' => null]) : '';
        }

        if ($ward = $wardModel($data->ward)){
            $ward->update(['visit_id' => $visit->id]);
        }


        $consultationsUpdate = [
            "admission_status"  => $data->admit,
            'ward'              => $ward->short_name ?? $visit->ward,
            "bed_no"            => $ward->bed_number ?? $visit->bed_no,
            "updated_by"        => $user?->id
        ];

        //update consultation
        $consultation->update($consultationsUpdate);

        $visitsUpdate = [
            'admission_status'  => $data->admit,
            'ward'              => $ward->short_name ?? $visit->ward,
            "bed_no"            => $ward->bed_number ?? $visit->bed_no,
            "ward_id"           => $ward->id ?? $visit->ward_id,
        ];

        if (!$visit->consulted) {
            $visitsUpdate['consulted'] = new Carbon();
        }

        //update visit
        return $visit->update($visitsUpdate);        
    }
}
