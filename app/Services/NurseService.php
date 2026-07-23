<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\User;
use App\Models\Visit;
use App\Services\HelperService;
use App\Services\PayPercentageService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class NurseService
{
    public function __construct(
        private readonly Visit $visit,
        private readonly PayPercentageService $payPercentageService,
        private readonly HelperService $helperService
        )
    {
        
    }

    // public function getpaginatedFilteredNurseVisits(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = "consulted";
    //     $orderDir   =  'desc';
    //     $query = $this->visit->select('id', 'patient_id', 'doctor_id', 'sponsor_id', 'doctor_done_by', 'consulted', 'admission_status', 'visit_type', 'discharge_reason', 'discharge_remark', 'closed', 'closed_opened_by', 'closed_opened_at', 'ward', 'bed_no', 'ward_id', 'discount', 'doctor_done_at')
    //     ->with([
    //         'sponsor:id,name,category_name,flag', 
    //         'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment,updated_by' 
    //         => with([
    //             'updatedBy:id,username' 
    //         ]), 
    //         'patient' => function($query){
    //             $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
    //             ->with(['flaggedBy:id,username']);
    //         }, 
    //         'antenatalRegisteration:id,visit_id', 
    //         'doctor:id,username', 
    //         'closedOpenedBy:id,username',
    //         'doctorDoneBy:id,username',
    //         'wards:id,visit_id,short_name,bed_number'
    //     ])
    //     ->withCount([
    //         'prescriptions as prescriptionsCharted' => function (Builder $query) {
    //         $query->where('chartable', true)
    //             ->where('discontinued', false)
    //             ->whereDoesntHave('medicationCharts')
    //             ->whereRelation('resource', 'sub_category', '=', 'Injectable');
    //         },
    //         'prescriptions as otherChartables' => function (Builder $query) {
    //         $query->where('chartable', true)
    //             ->where('discontinued', false)
    //             ->whereDoesntHave('nursingCharts')
    //             ->whereRelation('resource', 'sub_category', '!=', 'Injectable');
    //         },
    //         'prescriptions as otherPrescriptions' => function (Builder $query) {
    //         $query->where('chartable', false)
    //         ->where('chartable', false)
    //         ->where(function(Builder $query) {
    //             $query->whereRelation('resource', 'category', 'Medications')
    //                     ->orWhereRelation('resource', 'category', 'Consumables');
    //             });
    //         },
    //         'medicationCharts as doseCount',
    //         'medicationCharts as givenCount' => function (Builder $query) {
    //             $query->whereNotNull('dose_given');
    //         },
    //         'nursingCharts as scheduleCount',
    //         'nursingCharts as doneCount' => function (Builder $query) {
    //             $query->whereNotNull('time_done');
    //         },
    //         'vitalSigns as vitalSignsCount',
    //         'consultations as consultationsCount'
    //     ]);

    //     function applySearch(Builder $query, string $searchTermRaw) {
    //         $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

    //         return $query->where(function (Builder $query) use ($searchTerm, $searchTermRaw) {
    //             // 1. Direct Column Check (Visit table)
    //             if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $searchTermRaw)) {
    //                     $query->whereBetween('consulted', [$searchTermRaw . ' 00:00:00', $searchTermRaw . ' 23:59:59']);
    //                 } else {
    //                     $query->whereRaw('1 = 0'); 
    //                 }

    //             // 2. Patient Block (Uses Full-Text Index + Card No)
    //             $query->orWhereHas('patient', function ($q) use ($searchTerm, $searchTermRaw) {
    //                 $q->searchByName($searchTermRaw)
    //                 ->orWhere('card_no', 'LIKE', $searchTerm);
    //             });

    //             // 3. Consultations Block
    //             $query->orWhereHas('consultations', function ($q) use ($searchTerm) {
    //                 $q->where('icd11_diagnosis', 'LIKE', $searchTerm)
    //                 ->orWhere('admission_status', 'LIKE', $searchTerm);
    //             });

    //             // 4. Sponsor Block
    //             $query->orWhereHas('sponsor', function ($q) use ($searchTerm) {
    //                 $q->where('name', 'LIKE', $searchTerm)
    //                 ->orWhere('category_name', 'LIKE', $searchTerm);
    //             });
    //         });
    //     }

    //     if (!empty($params->searchTerm)) {
    //         $searchTermRaw = trim($params->searchTerm);
    //         $isPatientIdSearch = str_starts_with($searchTermRaw, 'pId-');
    //         $patientId = $isPatientIdSearch ? explode('-', $searchTermRaw)[1] : null;

    //         // 1. The Gatekeeper (ANC vs General)
    //         if ($data->filterBy === 'ANC') {
    //             $query->where('visit_type', 'ANC');
    //         } else {
    //             $query->whereNotNull('consulted');
    //         }

    //         // 2. The Integrated Search Block
    //         $query->where(function (Builder $sub) use ($isPatientIdSearch, $patientId, $query, $searchTermRaw) {
                
    //             if ($isPatientIdSearch) {
    //                 $sub->where('patient_id', $patientId);
    //             } else {
    //                 $query = applySearch($query, $searchTermRaw);
    //             }
    //         });

    //         return $query->orderBy($orderBy, $orderDir)
    //             ->paginate($params->length, '*', '', (($params->length + $params->start) / $params->length));
    //     }

    //     if ($data->filterBy == 'Outpatient'){
    //         return $query->whereNotNull('consulted')
    //         ->where('nurse_done_by', null)
    //         ->where('closed', false)
    //         ->where(function(Builder $query) {
    //             $query->whereRelation('prescriptions.resource', 'sub_category', '=', 'Injectable');
    //         })
    //         ->where('admission_status', '=', 'Outpatient')
    //         ->where('visit_type', '!=', 'ANC')
    //         ->orderBy($orderBy, $orderDir)
    //         ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->filterBy == 'Inpatient'){
    //         $nursesInpatients = $query->whereNotNull('consulted')
    //                 ->where('nurse_done_by', null)
    //                 ->where('closed', false)
    //                 ->where(function(Builder $query) {
    //                     $query->whereRelation('prescriptions.resource', 'category', '=', 'Medications')
    //                         ->orWhereRelation('prescriptions.resource', 'category', '=', 'Medical Services')
    //                         ->orWhereRelation('prescriptions', 'chartable', '=', '1');
    //                 })
    //                 ->where(function (Builder $query) {
    //                     $query->where('admission_status', '=', 'Inpatient')
    //                     ->orWhere('admission_status', '=', 'Observation');
    //                 })
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         return $nursesInpatients;
    //     }
    //     if ($data->filterBy == 'ANC'){
    //         return $query->where('nurse_done_by', null)
    //                 ->where('closed', false)
    //                 ->where('visit_type', '=', 'ANC')
    //                 ->orderBy('created_at', $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     return $query->whereNotNull('consulted')
    //                 ->where('nurse_done_by', null)
    //                 ->where('closed', false)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    // }

    // public function getPaginatedFilteredNurseVisits(DataTableQueryParams $params, $data)
    // {
    //     $searchTerm = trim($params->searchTerm ?? '');
        
    //     // 1. Base Query & Relationships
    //     $query = $this->visit->newQuery()
    //         ->select('id', 'patient_id', 'doctor_id', 'sponsor_id', 'doctor_done_by', 'consulted', 'admission_status', 'visit_type', 'discharge_reason', 'discharge_remark', 'closed', 'closed_opened_by', 'closed_opened_at', 'ward', 'bed_no', 'ward_id', 'discount', 'total_hms_bill', 'total_nhis_bill', 'total_paid', 'doctor_done_at')
    //         ->with([
    //             'sponsor:id,name,category_name,flag',
    //             'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment,updated_by',
    //             'latestConsultation.updatedBy:id,username',
    //             'patient' => fn($q) => $q->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')->with('flaggedBy:id,username'),
    //             'antenatalRegisteration:id,visit_id',
    //             'doctor:id,username',
    //             'closedOpenedBy:id,username',
    //             'doctorDoneBy:id,username',
    //             'wards:id,visit_id,short_name,bed_number'
    //         ])
    //         ->withCount([
    //             'prescriptions as prescriptionsCharted' => fn($q) => $q->where('chartable', true)->where('discontinued', false)->whereDoesntHave('medicationCharts')->whereRelation('resource', 'sub_category', 'Injectable'),
    //             'prescriptions as otherChartables' => fn($q) => $q->where('chartable', true)->where('discontinued', false)->whereDoesntHave('nursingCharts')->whereRelation('resource', 'sub_category', '!=', 'Injectable'),
    //             'prescriptions as otherPrescriptions' => fn($q) => $q->where('chartable', false)->whereRelation('resource', fn($r) => $r->whereIn('category', ['Medications', 'Consumables'])),
    //             'medicationCharts as doseCount',
    //             'medicationCharts as givenCount' => fn($q) => $q->whereNotNull('dose_given'),
    //             'nursingCharts as scheduleCount',
    //             'nursingCharts as doneCount' => fn($q) => $q->whereNotNull('time_done'),
    //             'vitalSigns as vitalSignsCount',
    //             'consultations as consultationsCount'
    //         ]);

    //     // 2. Handle Search (Priority)
    //     if (!empty($searchTerm)) {
    //         $this->applyNurseSearch($query, $searchTerm);
    //         // Per your request: No 'nurse_done_by' or 'closed' constraints during search
    //     } 
    //     // 3. Handle Filters (Only if NOT searching)
    //     else {
    //         $query->where('nurse_done_by', null)->where('closed', false);
    //         $this->applyNurseFilters($query, $data->filterBy);
    //     }

    //     // 4. Finalize Sorting and Pagination
    //     $orderBy = $data->filterBy === 'ANC' ? 'created_at' : 'consulted';
        
    //     return $query->orderBy($orderBy, 'desc')
    //         ->paginate(
    //             $params->length, 
    //             ['*'], 
    //             'page', 
    //             floor($params->start / $params->length) + 1
    //         );
    // }

    // /**
    //  * Extracted Search Logic
    //  */
    private function applyNurseSearch(Builder $query, string $searchTermRaw): void
    {
        if (str_starts_with($searchTermRaw, 'pId-')) {
            $query->where('patient_id', explode('-', $searchTermRaw)[1]);
            return;
        }

        $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

        $query->where(function (Builder $sub) use ($searchTerm, $searchTermRaw) {
            // Date Search
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $searchTermRaw)) {
                $sub->whereBetween('consulted', [$searchTermRaw . ' 00:00:00', $searchTermRaw . ' 23:59:59']);
            }

            // Patient Search
            $sub->orWhereHas('patient', fn($q) => 
                $q->searchByName($searchTermRaw)
                    ->orWhere('card_no', 'LIKE', $searchTerm)->orWhere('phone', 'LIKE', $searchTerm))
                // ->orWhereHas('consultations', function ($q) use ($searchTerm) {
                //     $q->where('icd11_diagnosis', 'LIKE', $searchTerm)
                //     ->orWhere('provisional_diagnosis', 'LIKE', $searchTerm)
                //     ->orWhere('admission_status', 'LIKE', $searchTerm);
                // })
                ->orWhereHas('sponsor', fn($q) => $q->where('name', 'LIKE', $searchTerm)->orWhere('category_name', 'LIKE', $searchTerm));
        });
    }

    // /**
    //  * Extracted Filter Logic
    //  */
    // private function applyNurseFilters(Builder $query, string $filterBy): void
    // {
    //     switch ($filterBy) {
    //         case 'ANC':
    //             $query->where('visit_type', 'ANC');
    //             break;

    //         case 'Outpatient':
    //             $query->whereNotNull('consulted')
    //                 ->where('admission_status', 'Outpatient')
    //                 ->where('visit_type', '!=', 'ANC')
    //                 ->whereRelation('prescriptions.resource', 'sub_category', 'Injectable');
    //             break;

    //         case 'Inpatient':
    //             $query->whereNotNull('consulted')
    //                 ->whereIn('admission_status', ['Inpatient', 'Observation'])
    //                 ->where(function (Builder $sub) {
    //                     // We check if the visit has ANY relevant prescription 
    //                     $sub->whereHas('prescriptions', function (Builder $p) {
    //                         $p->where('chartable', true) // Priority check: anything marked chartable
    //                             ->orWhereHas('resource', function (Builder $r) {
    //                                 // Or any specific categories that require nursing attention
    //                                 $r->whereIn('category', ['Medications', 'Medical Services']);
    //                             });
    //                     });
    //                 });
    //             break;

    //         default:
    //             $query->whereNotNull('consulted');
    //             break;
    //     }
    // }

    public function getPaginatedFilteredNurseVisits(DataTableQueryParams $params, $data)
    {
        $searchTerm = trim($params->searchTerm ?? '');
        
        $query = $this->visit->query()
            ->select([
                'visits.id', 'visits.patient_id', 'visits.doctor_id', 'visits.sponsor_id', 
                'visits.doctor_done_by', 'visits.nurse_done_by', 'visits.consulted', 'visits.admission_status', 
                'visits.visit_type', 'visits.discharge_reason', 'visits.discharge_remark', 
                'visits.closed', 'visits.closed_opened_by', 'visits.closed_opened_at', 
                'visits.ward', 'visits.bed_no', 'visits.ward_id', 'visits.discount', 
                'visits.total_hms_bill', 'visits.total_nhis_bill', 'visits.total_paid', 
                'visits.doctor_done_at', 'visits.nurse_done_at'
            ])
            ->with([
                'sponsor:id,name,category_name,flag',
                'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment,updated_by',
                'latestConsultation.updatedBy:id,username',
                'patient' => fn($q) => $q->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')->with('flaggedBy:id,username'),
                'antenatalRegisteration:id,visit_id',
                'doctor:id,username',
                'closedOpenedBy:id,username',
                'doctorDoneBy:id,username',
                'nurseDoneBy:id,username',
                'wards:id,visit_id,short_name,bed_number'
            ])
            ->withCount([
                'prescriptions as prescriptionsCharted' => fn($q) => $q->where('chartable', true)->where('discontinued', false)->whereDoesntHave('medicationCharts')->whereRelation('resource', 'sub_category', 'Injectable'),
                'prescriptions as otherChartables' => fn($q) => $q->where('chartable', true)->where('discontinued', false)->whereDoesntHave('nursingCharts')->whereRelation('resource', 'sub_category', '!=', 'Injectable'),
                'prescriptions as otherPrescriptions' => fn($q) => $q->where('chartable', false)->whereRelation('resource', fn($r) => $r->whereIn('category', ['Medications', 'Consumables'])),
                'medicationCharts as doseCount',
                'medicationCharts as givenCount' => fn($q) => $q->whereNotNull('dose_given'),
                'nursingCharts as scheduleCount',
                'nursingCharts as doneCount' => fn($q) => $q->whereNotNull('time_done'),
                'vitalSigns as vitalSignsCount',
                'consultations as consultationsCount',
                'nursesReports as reportCount'
            ]);

        if (!empty($searchTerm)) {
            $this->applyNurseSearch($query, $searchTerm);
        } else {
            // Apply Global constraints
            $query->whereNull('nurse_done_by')->where('closed', false)->whereNotNull('consulted');
            
            // Restore the specific filter branching
            match ($data->filterBy) {
                'ANC' => $query->where('visit_type', 'ANC'),

                'Outpatient' => $query->where('admission_status', 'Outpatient')
                                    ->where('visit_type', '!=', 'ANC')
                                    ->whereHas('prescriptions.resource', fn($q) => $q->where('sub_category', 'Injectable')),

                'Inpatient' => $query->whereIn('admission_status', ['Inpatient', 'Observation'])
                                    ->whereExists(function ($sub) {
                                        $sub->select(DB::raw(1))
                                            ->from('prescriptions')
                                            ->join('resources', 'prescriptions.resource_id', '=', 'resources.id')
                                            ->whereColumn('prescriptions.visit_id', 'visits.id')
                                            ->where(fn($p) => $p->where('prescriptions.chartable', true)
                                                                ->orWhereIn('resources.category', ['Medications', 'Medical Services']));
                                    }),

                default => $query, // Default already has consulted/closed/nurse_done_by applied
            };
        }

        $orderBy = $data->filterBy === 'ANC' ? 'created_at' : 'consulted';
        
        return $query->orderBy("visits.$orderBy", 'desc')
                    ->paginate($params->length, ['*'], 'page', floor($params->start / $params->length) + 1);
    }

    public function getConsultedVisitsNursesTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => $visit->consulted ? (new Carbon($visit->consulted))->format('d/m/y g:ia') : 'Not Seen Dr',
                'patient'           => $visit->patient?->patientId(),
                'patientId'         => $visit->patient?->id,
                'age'               => $visit->patient->age(),
                'doctor'            => $visit->doctor?->username,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'diagnosis'         => $visit->latestConsultation?->icd11_diagnosis ?? 
                                       $visit->latestConsultation?->provisional_diagnosis ?? 
                                       $visit->latestConsultation?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $visit->ward ? $this->helperService->displayWard($visit) : '',
                'wardId'            => $visit->ward_id ?? '',
                'wardPresent'       => $visit->wards?->visit_id == $visit->id,
                'updatedBy'         => $visit->latestConsultation?->updatedBy?->username ?? 'Nurse...',
                'conId'             => $visit->latestConsultation?->id,
                'visitType'         => $visit->visit_type,
                'vitalSigns'        => $visit->vitalSignsCount < 1 ? '' : $visit->vitalSignsCount,
                'ancVitalSigns'     => $visit->antenatalRegisteration?->ancVitalSigns->count(),
                'chartableMedications'  => $visit->prescriptionsCharted,
                'otherChartables'       => $visit->otherChartables,
                'otherPrescriptions'    => $visit->otherPrescriptions,
                'doseCount'         => $visit->doseCount,
                'givenCount'        => $visit->givenCount,
                'scheduleCount'     => $visit->scheduleCount,
                'doneCount'         => $visit->doneCount,
                'viewed'            => !!$visit->viewed_at,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'remark'            => $visit->discharge_remark ?? '',
                'doctorDone'        => $visit->doctorDoneBy?->username ?? '',
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'ancCount'          => $visit->visit_type == 'ANC' ? $visit->consultationsCount : '',
                'nurseDoneBy'       => $visit->nurseDoneBy?->username,
                'nurseDoneAt'       => $visit->nurse_done_at ? (new Carbon($visit->nurse_done_at))->format('d/m/y g:ia') : '',
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username,
                'closedAt'          => $visit->closed_opened_at ? (new Carbon($visit->closed_opened_at))->format('d/m/y g:ia') : '',
                'reportCount'       => $visit->reportCount < 1 ? '' : $visit->reportCount
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