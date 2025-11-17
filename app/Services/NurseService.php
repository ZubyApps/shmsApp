<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\User;
use App\Models\Visit;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class NurseService
{
    public function __construct(
        private readonly Visit $visit,
        private readonly PayPercentageService $payPercentageService,
        private readonly Ward $ward,
        private readonly HelperService $helperService
        )
    {
        
    }

    public function getpaginatedFilteredNurseVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = "consulted";
        $orderDir   =  'desc';
        $query = $this->visit::with([
            'sponsor', 
            'consultations.updatedBy', 
            'patient', 
            'vitalSigns', 
            'prescriptions', 
            'medicationCharts', 
            'antenatalRegisteration', 
            'doctor', 
            'closedOpenedBy',
            'nursingCharts',
            'payments',
            'doctorDoneBy',
        ])
        ->withCount([
            'prescriptions as prescriptionsCharted' => function (Builder $query) {
            $query->where('chartable', true)
                ->where('discontinued', false)
                ->whereDoesntHave('medicationCharts')
                ->whereRelation('resource', 'sub_category', '=', 'Injectable');
            },
            'prescriptions as otherChartables' => function (Builder $query) {
            $query->where('chartable', true)
                ->where('discontinued', false)
                ->whereDoesntHave('nursingCharts')
                ->whereRelation('resource', 'sub_category', '!=', 'Injectable');
            },
            'prescriptions as otherPrescriptions' => function (Builder $query) {
            $query->where('chartable', false)
            ->where('chartable', false)
            ->where(function(Builder $query) {
                $query->whereRelation('resource', 'category', 'Medications')
                      ->orWhereRelation('resource', 'category', 'Consumables');
            });
            },
        ]);


        if (! empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $patientId = explode('-', $searchTermRaw)[0] == 'pId' ? explode('-', $searchTermRaw)[1] : null;

            $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

            if ($data->filterBy == 'ANC'){
                $query->where('visit_type', '=', 'ANC');

                if ($patientId){ 
                    return $query->where('patient_id', $patientId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                return $query
                    // ->whereNotNull('consulted')
                    // ->where('visit_type', '=', 'ANC')
                    ->where(function (Builder $query) use($searchTerm) {
                        $query->where('created_at', 'LIKE', $searchTerm)
                        ->orWhere(function($q) use ($searchTerm) {
                            $terms = array_filter(explode(' ', trim($searchTerm)));
                            foreach ($terms as $term) {
                                $q->where(function($subQuery) use ($term) {
                                    $subQuery->whereRelation('patient', 'first_name', 'LIKE', $term)
                                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $term)
                                            ->orWhereRelation('patient', 'last_name', 'LIKE', $term);
                                });
                            }
                        })
                        // ->orWhereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                        // ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                        // ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'provisional_diagnosis', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($patientId){ 
                return $query->where('patient_id', $patientId)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->whereNotNull('consulted')
                    ->where(function (Builder $query) use($searchTerm) {
                        $query->where('created_at', 'LIKE', $searchTerm)
                        ->orWhere(function($q) use ($searchTerm) {
                            $terms = array_filter(explode(' ', trim($searchTerm)));
                            foreach ($terms as $term) {
                                $q->where(function($subQuery) use ($term) {
                                    $subQuery->whereRelation('patient', 'first_name', 'LIKE', $term)
                                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $term)
                                            ->orWhereRelation('patient', 'last_name', 'LIKE', $term);
                                });
                            }
                        })
                        // ->orWhereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                        // ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                        // ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'provisional_diagnosis', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Outpatient'){
            return $query->whereNotNull('consulted')
            ->where('nurse_done_by', null)
            ->where('closed', false)
            ->where(function(Builder $query) {
                $query->whereRelation('prescriptions.resource', 'sub_category', '=', 'Injectable');
            })
            ->where('admission_status', '=', 'Outpatient')
            ->where('visit_type', '!=', 'ANC')
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            $nursesInpatients = $query->whereNotNull('consulted')
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
            return $nursesInpatients;
        }
        if ($data->filterBy == 'ANC'){
            return $query->where('nurse_done_by', null)
                    ->where('closed', false)
                    ->where('visit_type', '=', 'ANC')
                    ->orderBy('created_at', $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->whereNotNull('consulted')
                    ->where('nurse_done_by', null)
                    ->where('closed', false)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getConsultedVisitsNursesTransformer(): callable
    {
       return  function (Visit $visit) {
        $latestConsultation = $visit->consultations->sortDesc()->first();
        $ward = $this->ward->where('id', $visit->ward)->first();

            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient?->patientId(),
                'patientId'         => $visit->patient?->id,
                'age'               => $visit->patient->age(),
                'doctor'            => $visit->doctor?->username,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'diagnosis'         => $latestConsultation?->icd11_diagnosis ?? 
                                       $latestConsultation?->provisional_diagnosis ?? 
                                       $latestConsultation?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $ward ? $this->helperService->displayWard($ward) : '',
                'wardId'            => $visit->ward ?? '',
                'wardPresent'       => $ward?->visit_id == $visit->id,
                'updatedBy'         => $latestConsultation?->updatedBy?->username ?? 'Nurse...',
                'conId'             => $latestConsultation?->id,
                'visitType'       => $visit->visit_type,
                'vitalSigns'        => $visit->vitalSigns->count(),
                'ancVitalSigns'     => $visit->antenatalRegisteration?->ancVitalSigns->count(),
                'chartableMedications'  => $visit->prescriptionsCharted,
                'otherChartables'       => $visit->otherChartables,
                'otherPrescriptions'    => $visit->otherPrescriptions,
                'doseCount'         => $visit->medicationCharts->count(),
                'givenCount'        => $visit->medicationCharts->where('dose_given', '!=', null)->count(),
                'scheduleCount'     => $visit->nursingCharts->count(),
                'doneCount'         => $visit->nursingCharts->where('time_done', '!=', null)->count(),
                'viewed'            => !!$visit->viewed_at,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'remark'            => $visit->discharge_remark ?? '',
                'doctorDone'        => $visit->doctorDoneBy?->username ?? '',
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'ancCount'          => $visit->visit_type == 'ANC' ? $visit->consultations->count() : '',
                'nurseDoneBy'       => $visit->nurseDoneBy?->username,
                'nurseDoneAt'       => $visit->nurse_done_at ? (new Carbon($visit->nurse_done_at))->format('d/m/y g:ia') : '',
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username,
                'visitType'         => $visit->visit_type,
            ];
         };
    }

    public function done(Visit $visit, User $user)
    {
        if ($visit->nurse_done_by){
            return $visit->update([
                'nurse_done_by' => null,
                'nurse_done_at' => null,
            ]);
        }
        return $visit->update([
            'nurse_done_by' => $user->id,
            'nurse_done_at' => new Carbon()
        ]);
    }
}