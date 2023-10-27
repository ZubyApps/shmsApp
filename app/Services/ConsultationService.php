<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Patient;
use App\Models\User;
use App\Models\Consultation;
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
                "obgyn_history"     => $data->obyGyneHistory,
                "obgyn_history"     => $data->obyGyneHistory,
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
                "roppt_brim"        => $data->ropPartToBrim,
            ]);  

            $consultation->visit()->update([
                'status'    => true,
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
                "obgyn_history"     => $data->obyGyneHistory,
                "obgyn_history"     => $data->obyGyneHistory,
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

    public function getPaginatedPatients(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';

        if (! empty($params->searchTerm)) {
            return $this->consultation
                        ->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->consultation
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Consultation $consultation) {
            return [
                'id'                => $consultation->id,
                'patientId'         => $consultation->patient->id,
                'patient'           => $consultation->patient->card_no.' ' .$consultation->patient->first_name.' '. $consultation->patient->middle_name.' '.$consultation->patient->last_name,
                'sex'               => $consultation->patient->sex,
                'age'               => (new Carbon($consultation->patient->date_of_birth))->age.'yrs',
                'sponsor'           => $consultation->patient->sponsor->name,
                'came'              => (new Carbon($consultation->created_at))->diffForHumans(),
                'doctor'            => $consultation->doctor ?? '',
                'patientType'       => $consultation->patient->patient_type,
                'status'            => $consultation->status
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
}