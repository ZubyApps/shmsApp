<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class NurseService
{
    public function __construct(
        private readonly Visit $visit,
        private readonly PayPercentageService $payPercentageService
        )
    {
        
    }

    public function getpaginatedFilteredNurseVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = "consulted";
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
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

        if ($data->filterBy == 'Outpatient'){
            return $this->visit
            ->where('consulted', '!=', null)
            ->where('nurse_done_by', null)
            ->where('closed', false)
            ->where(function(Builder $query) {
                $query->whereRelation('prescriptions.resource', 'category', '=', 'Medications')
                    ->orWhereRelation('prescriptions.resource', 'category', '=', 'Medical Services');
            })
            ->where('admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('nurse_done_by', null)
                    ->where('closed', false)
                    ->where(function(Builder $query) {
                        $query->whereRelation('prescriptions.resource', 'category', '=', 'Medications')
                            ->orWhereRelation('prescriptions.resource', 'category', '=', 'Medical Services')
                            ->orWhereRelation('prescriptions', 'chartable', '=', '1');
                    })
                    ->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $this->visit
                    ->where('nurse_done_by', null)
                    ->where('closed', false)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('nurse_done_by', null)
                    ->where('closed', false)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getConsultedVisitsNursesTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient?->patientId(),
                'patientId'         => $visit->patient?->id,
                'age'               => $visit->patient->age(),
                'doctor'            => $visit->doctor?->username,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $visit->ward ?? '',
                'bedNo'             => $visit->bed_no ?? '',
                'updatedBy'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->updatedBy?->username ?? 'Nurse...',
                'conId'             => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->id,
                'patientType'       => $visit->patient->patient_type,
                'vitalSigns'        => $visit->vitalSigns->count(),
                'ancVitalSigns'     => $visit->antenatalRegisteration?->ancVitalSigns->count(),
                'chartableMedications'  => (new Prescription())->prescriptionsCharted($visit->id, 'medicationCharts'),
                'otherChartables'       => (new Prescription())->prescriptionsCharted($visit->id, 'nursingCharts', '!='),
                'doseCount'         => $visit->medicationCharts->count(),
                'givenCount'        => $visit->medicationCharts->where('dose_given', '!=', null)->count(),
                'scheduleCount'     => $visit->nursingCharts->count(),
                'doneCount'         => $visit->nursingCharts->where('time_done', '!=', null)->count(),
                'viewed'            => !!$visit->viewed_at,
                'sponsorCategory'   => $visit->sponsor->sponsorCategory?->name,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'remark'            => $visit->discharge_remark ?? '',
                'doctorDone'        => $visit->doctorDoneBy?->username ?? '',
                'ancCount'          => explode(".", $visit->patient->patient_type)[0] == 'ANC' ? $visit->consultations->count() : '',
                'nurseDoneBy'       => $visit->nurseDoneBy?->username,
                'closed'            => $visit->closed
            ];
         };
    }

    public function done(Visit $visit, User $user)
    {
        if ($visit->nurse_done_by){
            return $visit->update([
                'nurse_done_by' => null
            ]);
        }
        return $visit->update([
            'nurse_done_by' => $user->id
        ]);
    }
}