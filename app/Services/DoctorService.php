<?php

declare(strict_types=1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Http\Resources\PatientBioResource;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class DoctorService
{    
    public function __construct(
        private readonly Visit $visit,
        private readonly PayPercentageService $payPercentageService,
        private readonly HelperService $helperService
    ) {}

    private function baseQuery(): Builder
    {
        return $this->visit
        ->select('id', 'patient_id', 'doctor_id', 'sponsor_id', 'doctor_done_by', 'consulted', 'admission_status', 'visit_type', 'discharge_reason', 'discharge_remark', 'closed', 'closed_opened_by', 'closed_opened_at', 'ward', 'bed_no', 'ward_id', 'waiting_for', 'discount', 'doctor_done_at')->with([
            'sponsor:id,name,category_name,flag',
            'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment' => with(['updatedBy' => function ($query) {
                $query->select('id', 'username');
            }]), 
            'patient' => function($query) {
                $query->select('id', 'sex', 'flag', 'flag_reason', 'first_name', 'middle_name', 'last_name', 'card_no', 'date_of_birth', 'flagged_by', 'flagged_at')
                ->with(['flaggedBy:id,username'])
                ->withCount([
                    'visits as visitsCount' => function (Builder $query) {
                    $query->where('consulted', '>', Carbon::now()->subDays(30));
                    },
                ]);
            },
            'antenatalRegisteration:id,visit_id'=> with(['ancVitalSigns' => function ($query) {
                $query->select('id', 'antenatal_registeration_id');
            }]),
            'doctor:id,username',
            'closedOpenedBy:id,username',
            'doctorDoneBy:id,username',
            'wards:id,visit_id,short_name,bed_number'
        ])
        ->withCount([
            'prescriptions as labPrescribed' => function (Builder $query) {
                $query->whereRelation('resource', 'category', '=', 'Investigations');
            },
            'prescriptions as labDone' => function (Builder $query) {
                $query->whereRelation('resource', 'category', '=', 'Investigations')
                    ->where('result_date', '!=', null);
            },
            'prescriptions as prescriptionsCharted' => function (Builder $query) {
                $query->where('chartable', true)
                    ->where('discontinued', false)
                    ->whereDoesntHave('medicationCharts')
                    ->whereRelation('resource', 'sub_category', '=', 'Injectable');
            },
            'medicationCharts as doseCount',
            'medicationCharts as givenCount' => function (Builder $query) {
                $query->whereNotNull('dose_given');
            },
             'vitalSigns as vitalSignsCount',
             'consultations as consultationsCount'
        ])
        ->whereNotNull('consulted');
    }

    // private function applySearch(Builder $query, string $searchTerm): Builder
    // {
    //     $searchTerm = '%' . addcslashes($searchTerm, '%_') . '%';
    //     return $query->where(function (Builder $query) use ($searchTerm) {
    //         $query->where('created_at', 'LIKE', $searchTerm)
    //             ->orWhereRelation('patient', 'first_name', 'LIKE', $searchTerm)
    //             ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
    //             ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
    //             ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
    //             ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', $searchTerm)
    //             ->orWhereRelation('consultations', 'provisional_diagnosis', 'LIKE', $searchTerm)
    //             ->orWhereRelation('consultations', 'admission_status', 'LIKE', $searchTerm)
    //             ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
    //             ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
    //     });
    // }

    private function applySearch(Builder $query, string $searchTerm): Builder
    {
        $searchTerm = trim($searchTerm);

        if (explode('-', $searchTerm)[0] == 'pId'){
            return  $query->where('patient_id', explode('-', $searchTerm)[1]);
        }
        $searchTerm = '%' . addcslashes(trim($searchTerm), '%_') . '%';
        return $query->where(function (Builder $query) use ($searchTerm) {
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
                ->orWhereRelation('patient', 'phone', 'LIKE', $searchTerm)
                ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm);
                // ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', $searchTerm)
                // ->orWhereRelation('consultations', 'provisional_diagnosis', 'LIKE', $searchTerm)
                // ->orWhereRelation('consultations', 'admission_status', 'LIKE', $searchTerm)
                // ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                // ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
        });
    }

    private function generalFilters(Builder $query, string $method)
    {
        if ($method == 'outPatients') {
            $query = $query->where('admission_status', 'Outpatient')
                        ->where('visit_type', '!=', 'ANC');
        }
        if ($method == 'inPatients') {
            $query = $query->where(function (Builder $query) {
                $query->where('admission_status', '=', 'Inpatient')
                    ->orWhere('admission_status', '=', 'Observation');
            });
        }
        return $query->where('doctor_done_by', null)
            ->where('closed', false);
    }

    public function getPaginatedOutpatientConsultedVisits($data, DataTableQueryParams $params, User $user)
    {
        $query = $this->baseQuery();

        if (!empty($params->searchTerm)) {
            $query = $this->applySearch($query, $params->searchTerm);
            return $this->helperService->paginateQuery($query, $params);
        }

        if ($data->filterBy == 'My Patients') {
            $query->where('doctor_id', $user->id);
        }

        $query = $this->generalFilters($query, 'outPatients');
        
        return $this->helperService->paginateQuery($query, $params);
    }

    public function getPaginatedInpatientConsultedVisits($data, DataTableQueryParams $params, User $user)
    {
        $query = $this->baseQuery();

        if (!empty($params->searchTerm)) {
            $query = $this->applySearch($query, $params->searchTerm);
            return $this->helperService->paginateQuery($query, $params);
        }

        if ($data->filterBy == 'My Patients') {
            $query->where('doctor_id', $user->id);   
        }

        $query = $this->generalFilters($query, 'inPatients');

        return $this->helperService->paginateQuery($query, $params);
    }

    public function getPaginatedAncConsultedVisits($data, DataTableQueryParams $params, User $user)
    {
        $query = $this->baseQuery()->where('visit_type', '=', 'ANC');

        if (!empty($params->searchTerm)) {
            $query = $this->applySearch($query, $params->searchTerm);
            return $this->helperService->paginateQuery($query, $params);
        }

        if ($data->filterBy == 'My Patients') {
            $query->where('doctor_id', $user->id);
        }

        $query = $this->generalFilters($query, '');

        return $this->helperService->paginateQuery($query, $params);
    }

    public function getConsultedVisitsTransformer(): callable
    {
        return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'patientId'         => $visit->patient->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'age'               => $visit->patient->age(),
                'sex'               => $visit->patient->sex,
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => $visit->latestConsultation?->icd11_diagnosis ?? $visit->latestConsultation?->provisional_diagnosis ?? $visit->latestConsultation?->assessment,
                'selectedDiagnosis'     => $visit->latestConsultation?->icd11_diagnosis ?? '',
                'provisionalDiagnosis'  => $visit->latestConsultation?->provisional_diagnosis ?? '',
                'conId'             => $visit->latestConsultation?->id,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
                'vitalSigns'        => $visit->vitalSignsCount,
                'ancVitalSigns'     => $visit->antenatalRegisteration?->ancVitalSigns->count(),
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $visit->ward ? $this->helperService->displayWard($visit) : '',
                'wardId'            => $visit->ward_id ?? '',
                'wardPresent'       => $visit->wards?->visit_id == $visit->id,
                'updatedBy'         => $visit->latestConsultation?->updatedBy?->username ?? 'Nurse...',
                'visitType'         => $visit->visit_type,
                'labPrescribed'     => $visit->labPrescribed,
                'labDone'           => $visit->labDone,
                'chartableMedications'  => $visit->prescriptionsCharted,
                'doseCount'         => $visit->doseCount,
                'givenCount'        => $visit->givenCount,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'remark'            => $visit->discharge_remark ?? '',
                '30dayCount'        => $visit->patient->visitsCount . ' visit(s)',
                'doctorDone'        => $visit->doctorDoneBy->username ?? '',
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'ancCount'          => $visit->visit_type == 'ANC' ? $visit->consultationsCount : '',
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username,
                'closedAt'          => $visit->closed_opened_at ? (new Carbon($visit->closed_opened_at))->format('d/m/y g:ia') : '',
            ];
        };
    }

    public function initiateConsultation(Visit $visit, Request $request)
    {
        $visit->update([
            'doctor_id'   =>  $request->user()->id
        ]);

        return response()->json(new PatientBioResource($visit));
    }

    public function initiateReview(Visit $visit, Request $request)
    {
        return response()->json(new PatientBioResource($visit));
    }
}
