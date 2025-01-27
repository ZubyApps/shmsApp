<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Http\Resources\PatientBioResource;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DoctorService
{
    public function __construct(
        private readonly Visit $visit,
        private readonly PayPercentageService $payPercentageService,
        private readonly Ward $ward,
        private readonly HelperService $helperService
        )
    {
        
    }

    public function getPaginatedOutpatientConsultedVisits($data, DataTableQueryParams $params, User $user)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) use($params) {
                        $query->where('created_at', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'My Patients'){
            return $this->visit
            ->where('consulted', '!=', null)
            ->where('doctor_id', '=', $user->id)
            ->where('doctor_done_by', null)
            ->where('closed', false)
            ->where('admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->where('closed', false)
                    ->where('admission_status', '=', 'Outpatient')
                    ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPaginatedInpatientConsultedVisits($data, DataTableQueryParams $params, User $user)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) use($params) {
                        $query->where('created_at', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        // ->orWhereRelation('consultations', 'admission_status', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'My Patients'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->where('closed', false)
                    ->where('doctor_id', '=', $user->id)
                    ->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->where('closed', false)
                    ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                    ->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPaginatedAncConsultedVisits($data, DataTableQueryParams $params, User $user)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->where(function (Builder $query) use($params) {
                        $query->where('created_at', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        // ->orWhereRelation('consultations', 'admission_status', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'My Patients'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->where('doctor_id', '=', $user->id)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->where('closed', false)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('doctor_done_by', null)
                    ->where('closed', false)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getConsultedVisitsTransformer(): callable
    {
       return  function (Visit $visit) {
        $latestConsultation = $visit->consultations->sortDesc()->first();
        $ward               = $this->ward->where('id', $visit->ward)->first();

        return [
                'id'                => $visit->id,
                'patientId'         => $visit->patient->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'age'               => $visit->patient->age(),
                'sex'               => $visit->patient->sex,
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => $latestConsultation?->icd11_diagnosis ?? $latestConsultation?->provisional_diagnosis ?? $latestConsultation?->assessment,
                'selectedDiagnosis'     => $latestConsultation?->icd11_diagnosis ?? '',
                'provisionalDiagnosis'  => $latestConsultation?->provisional_diagnosis ?? '',
                'conId'             => $latestConsultation?->id,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient->flag_reason,
                'vitalSigns'        => $visit->vitalSigns->count(),
                'ancVitalSigns'     => $visit->antenatalRegisteration?->ancVitalSigns->count(),
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $ward ? $this->helperService->displayWard($ward) : '',
                'wardId'            => $visit->ward ?? '',
                'wardPresent'       => $ward?->visit_id == $visit->id,
                'updatedBy'         => $latestConsultation?->updatedBy?->username ?? 'Nurse...',
                'patientType'       => $visit->patient->patient_type,
                'labPrescribed'     => Prescription::where('visit_id', $visit->id)
                                        ->whereRelation('resource', 'category', '=', 'Investigations')
                                        ->count(),
                'labDone'           => Prescription::where('visit_id', $visit->id)
                                        ->whereRelation('resource', 'category', '=', 'Investigations')
                                        ->where('result_date','!=', null)
                                        ->count(),
                'chartableMedications'  => (new Prescription())->prescriptionsCharted($visit->id, 'medicationCharts'),
                'doseCount'         => $visit->medicationCharts->count(),
                'givenCount'        => $visit->medicationCharts->where('dose_given', '!=', null)->count(),
                'sponsorCategory'   => $visit->sponsor->sponsorCategory->name,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'remark'            => $visit->discharge_remark ?? '',
                '30dayCount'        => $visit->patient->visits->where('consulted', '>', (new Carbon())->subDays(30))->count().' visit(s)',
                'doctorDone'        => $visit->doctorDoneBy->username ?? '',
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'ancCount'          => explode(".", $visit->patient->patient_type)[0] == 'ANC' ? $visit->consultations->count() : '',
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username

            ];
         };
    }

    public function initiateConsultation(Visit $visit, Request $request) 
    {
        $visit->update([
            'doctor_id'    =>  $request->user()->id
        ]);

        return response()->json(new PatientBioResource($visit));
    }

    public function initiateReview(Visit $visit, Request $request) 
    {
        return response()->json(new PatientBioResource($visit));
    }
}