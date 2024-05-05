<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\User;
use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationService
{
    public function __construct(private readonly Consultation $consultation, private readonly Visit $visit)
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
                "bed_no"                    => $data->bedNumber,
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

            if ($visit->consulted){
                $visit->update([
                    'admission_status'  => $data->admit,
                    'ward'              => $data->ward ? $data->ward : $visit->ward,
                    'bed_no'            => $data->bedNumber ? $data->bedNumber : $visit->bed_no,
                ]);
            } else {
                $consultation->visit->update([
                    'consulted'         => new Carbon(),
                    'admission_status'  => $data->admit,
                    'ward'              => $data->ward,
                    'bed_no'            => $data->bedNumber,
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
                "bed_no"                    => $data->bedNumber,
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

            if ($visit->consulted){
                $visit()->update([
                    'admission_status'  => $data->admit,
                    'ward'              => $data->ward ? $data->ward : $visit->ward,
                    'bed_no'            => $data->bedNumber ? $data->bedNumber : $visit->bed_no,
                ]);
            } else {
                $visit()->update([
                    'consulted'         => new Carbon(),
                    'admission_status'  => $data->admit,
                    'ward'              => $data->ward,
                    'bed_no'            => $data->bedNumber,
                ]);
            }

            return $consultation;
        });
    }

    public function updateAdmissionStatus(Consultation $consultation, Request $data, User $user)
    {
        return DB::transaction(function () use ($data, $consultation, $user) {
            
            if ($data->admit){
                $consultation->update([
                    "admission_status"          => $data->admit,
                    "ward"                      => $data->ward,
                    "bed_no"                    => $data->bedNumber,
                    "updated_by"                => $user->id
                ]);
            }
            $consultation->update([
                "ward"                      => $data->ward,
                "bed_no"                    => $data->bedNumber,
                "updated_by"                => $user->id
            ]);
    
            return $consultation->visit()->update([
                'admission_status'  => $data->admit,
                'ward'              => $data->ward,
                'bed_no'            => $data->bedNumber,
            ]);
        });
    }

    public function getConsultations(Request $request, Visit $visit)
    {
        return DB::transaction(function () use ($request, $visit) {

            // $visit->update([
            //     'viewed_at' => new Carbon(),
            //     'viewed_by' => $request->user()->id,
            // ]);

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
}
