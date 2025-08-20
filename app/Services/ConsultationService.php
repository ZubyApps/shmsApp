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
                "ward"                      => $data->ward,
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

            $this->determineWard($visit, $data);

            if ($visit->consulted){
                $visit->update([
                    'admission_status'  => $data->admit,
                    'ward'              => $data->ward ?? $visit->ward,
                ]);
            } else {
                $visit->update([
                    'consulted'         => new Carbon(),
                    'admission_status'  => $data->admit,
                    'ward'              => $data->ward,
                ]);
            }
     
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
                "ward"                      => $data->ward,
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
                "user_id"                   => $user->id
            ]);

            $visit = $consultation->visit;

            $this->determineWard($visit, $data);

            if ($visit->consulted){
                $visit->update([
                    'admission_status'  => $data->admit,
                    'ward'              => $data->ward ?? $visit->ward,
                ]);
            } else {
                $visit()->update([
                    'consulted'         => new Carbon(),
                    'admission_status'  => $data->admit,
                    'ward'              => $data->ward,
                ]);
            }

            return $consultation;
        });
    }

    public function updateAdmissionStatus(Consultation $consultation, Request $data, User $user)
    {
        return DB::transaction(function () use ($data, $consultation, $user) {

            $visit      = $consultation->visit;

            if ($data->admit == 'Outpatient'){
                $consultation->update([
                    "admission_status"          => $data->admit,
                    "updated_by"                => $user->id
                ]);

                $this->determineWard($visit, $data);

                return $visit->update([
                    'admission_status'  => $data->admit,
                ]);

            } else {

                $this->determineWard($visit, $data);

                $consultation->update([
                    "admission_status"          => $data->admit,
                    "ward"                      => $data->ward,
                    "updated_by"                => $user->id
                ]);

                return $visit->update([
                    'admission_status'  => $data->admit,
                    'ward'              => $data->ward,
                ]);
            }

        });
    }

    public function getConsultations(Request $request, Visit $visit)
    {
        return DB::transaction(function () use ($request, $visit) {

            return $this->consultation
                        ->where('visit_id', $visit->id)
                        ->orderBy('created_at', 'asc')
                        ->get();
        });
    }

    public function getVisitsAndConsultations(Request $request, Patient $patient)
    {
        return $this->visit
                    ->where('patient_id', $patient->id)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }

    public function determineWard($visit, $data)
    {
        $wardModel = fn($wardId)=>$this->ward::find($wardId);

        if ($ward = $wardModel($visit->ward)){
            $ward->visit_id == $visit->id ? $ward->update(['visit_id' => null]) : '';
        }

        if ($ward = $wardModel($data->ward)){
            $ward->update(['visit_id' => $visit->id]);
        }
    }
}
