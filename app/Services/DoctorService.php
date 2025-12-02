<?php

declare(strict_types=1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Http\Resources\PatientBioResource;
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
    ) {}

    private function baseQuery(): Builder
    {
        return $this->visit::with([
            'sponsor',
            'consultations.updatedBy',
            'patient.visits',
            'vitalSigns',
            'prescriptions',
            'medicationCharts',
            'antenatalRegisteration.ancVitalSigns',
            'doctor',
            'closedOpenedBy',
            'doctorDoneBy',
            'payments'
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
            }
        ])
        ->whereNotNull('consulted');
    }

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
            $latestConsultation = $visit->consultations->sortDesc()->first();
            $ward               = $this->ward->find($visit->ward);

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
                'visitType'         => $visit->visit_type,
                'labPrescribed'     => $visit->labPrescribed,
                'labDone'           => $visit->labDone,
                'chartableMedications'  => $visit->prescriptionsCharted,
                'doseCount'         => $visit->medicationCharts->count(),
                'givenCount'        => $visit->medicationCharts->whereNotNull('dose_given')->count(),
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'remark'            => $visit->discharge_remark ?? '',
                '30dayCount'        => $visit->patient->visits->where('consulted', '>', (new Carbon())->subDays(30))->count() . ' visit(s)',
                'doctorDone'        => $visit->doctorDoneBy->username ?? '',
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'ancCount'          => $visit->visit_type == 'ANC' ? $visit->consultations->count() : '',
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username,
                'visitType'         => $visit->visit_type,

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
