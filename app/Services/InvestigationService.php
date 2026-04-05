<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Prescription;
use App\Models\User;
use App\Models\Visit;
use App\Notifications\TestResultNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\PayPercentageService;

class InvestigationService
{
    public function __construct(
        private readonly Visit $visit, 
        private readonly Prescription $prescription,
        private readonly PayPercentageService $payPercentageService,
        private readonly HelperService $helperService,
        )
    {
        
    }

    public function getpaginatedFilteredLabVisits(DataTableQueryParams $params, $data)
    {
        $query = $this->visit
            ->select('id', 'patient_id', 'doctor_id', 'sponsor_id', 'doctor_done_by', 'consulted', 'admission_status', 'visit_type', 'discharge_reason', 'discharge_remark', 'closed', 'closed_opened_by', 'closed_opened_at', 'ward', 'bed_no', 'ward_id', 'discount', 'total_hms_bill', 'total_nhis_bill', 'total_paid', 'doctor_done_at')
            ->with([
                'sponsor:id,name,category_name,flag', 
                'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment', 
                'patient' => function($query){
                    $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                    ->with(['flaggedBy:id,username']);
                },
                'doctor:id,username',
                'doctorDoneBy:id,username',
                'closedOpenedBy:id,username',
                'wards:id,visit_id,short_name,bed_number'
        ])
        ->withCount([
            'prescriptions as labPrescribed' => function (Builder $query) {
            $query->labInvestigations();
            }, 
            'prescriptions as labDone' => function (Builder $query) {
            $query->labInvestigations()
                    ->where('result_date', '!=', null);
            },
        ])
        ->withExists(['investigationsList as isOnList'])
        ->whereNotNull('consulted');

        if (!empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

            // 1. Apply the ANC "Gatekeeper" if needed
            if ($data->filterBy == 'ANC') {
                $query->where('visit_type', 'ANC');
            }

            if (str_starts_with($searchTermRaw, 'pId-')) {
                $query->where('patient_id', explode('-', $searchTermRaw)[1]);
                return $this->helperService->paginateQuery($query, $params);
            }

            // 2. The Consolidated Search Block
            $query->where(function (Builder $query) use ($searchTerm, $searchTermRaw) {
                $query->whereBetween('consulted', [$searchTermRaw . ' 00:00:00', $searchTermRaw . ' 23:59:59'])
                    ->orWhereHas('patient', function ($q) use ($searchTerm, $searchTermRaw) {
                        $q->searchByName($searchTermRaw) 
                        ->orWhere('card_no', 'LIKE', $searchTerm);
                    });
            });

            // 3. Return using your helper service (or manual pagination)
            return $this->helperService->paginateQuery($query, $params);
        }

        if ($data->filterBy == 'Outpatient'){
            $query->where('admission_status', '=', 'Outpatient')
                ->where('visit_type', '!=', 'ANC');
        }

        if ($data->filterBy == 'Inpatient'){
            $query->inpatientOrObservation();
        }
        if ($data->filterBy == 'ANC'){
            $query->where('visit_type', '=', 'ANC');
        }

        $query = $this->generalFilters($query);
        return $this->helperService->paginateQuery($query, $params);
    }

    private function generalFilters(Builder $query)
    {
        return $query->where('closed', false)
                    ->whereHas('prescriptions', function(Builder $query){
                        $query->where('result', '=', null)
                        ->where('dispense_comment', null)
                        // ->where('discontinued', false)
                        ->labInvestigations();
                    });
    }

    public function getConsultedVisitsLabTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => $visit->latestConsultation?->icd11_diagnosis ?? 
                                       $visit->latestConsultation?->provisional_diagnosis ?? 
                                       $visit->latestConsultation?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $visit->ward ? $this->helperService->displayWard($visit) : '',
                'wardId'            => $visit->ward_id ?? '',
                'wardPresent'       => $visit->wards?->visit_id == $visit->id,
                'visitType'         => $visit->visit_type,
                'labPrescribed'     => $visit->labPrescribed,
                'labDone'           => $visit->labDone,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'doctorDone'        => $visit->doctorDoneBy->username ?? '',
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username,
                'closedAt'          => $visit->closed_opened_at ? (new Carbon($visit->closed_opened_at))->format('d/m/y g:ia') : '',
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
                'isOnList'          => $visit->isOnList
            ];
         };
    }

    // public function getInpatientLabRequests(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'created_at';
    //     $orderDir   =  'desc';
    //     $query = $this->prescription->select('id', 'resource_id', 'user_id', 'visit_id', 'consultation_id', 'created_at', 'result_date', 'approved', 'rejected', 'paid', 'discontinued', 'hms_bill', 'nhis_bill', 'discontinued_by', 'sample_collected_at', 'sample_collected_by')
    //                     ->with([
    //                         'resource:id,name,category', 
    //                         'user:id,username', 
    //                         'visit' => function ($query) {
    //                             $query->select('id', 'sponsor_id', 'patient_id')
    //                                 ->with([
    //                                 'sponsor' => function ($query){
    //                                     $query->select('id', 'name', 'category_name', 'sponsor_category_id')
    //                                         ->with(['sponsorCategory:id,pay_class']);
    //                                 },
    //                                 'patient:id,first_name,middle_name,last_name,card_no'
    //                             ]);
    //                         },
    //                         'consultation:id,icd11_diagnosis,provisional_diagnosis,assessment',
    //                         'discontinuedBy:id,username',
    //                         'sampleCollectedBy:id,username'
    //                     ]);

    //     if (!empty($params->searchTerm)) {
    //         $searchTermRaw = trim($params->searchTerm);
    //         $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

    //         return $query->labInvestigations()
    //             // 1. Grouped Patient Search via Relationship
    //             ->whereHas('visit.patient', function (Builder $q) use ($searchTerm, $searchTermRaw) {
    //                 $q->searchByName($searchTermRaw) // Using the Full-Text Index!
    //                 ->orWhere('phone', 'LIKE', $searchTerm)
    //                 ->orWhere('card_no', 'LIKE', $searchTerm);
    //             })
    //             // 2. Gatekeepers
    //             ->whereRelation('visit', 'consulted', '!=', null)
    //             ->whereNull('result_date')
    //             ->orderBy($orderBy, $orderDir)
    //             ->paginate($params->length, ['*'], 'page', ($params->length + $params->start) / $params->length);
    //     }

    //     return $query->labInvestigations()
    //                 ->whereRelation('visit', 'consulted', '!=', null)
    //                 ->where(function (Builder $query) {
    //                     $query->whereRelation('consultation', 'admission_status', '=', 'Inpatient')
    //                     ->orWhereRelation('consultation', 'admission_status', '=', 'Observation');
    //                 })
    //                 ->where('result_date', null)
    //                 // ->where('discontinued', false)
    //                 ->where('dispense_comment', null)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    // }

    public function getInpatientLabRequests(DataTableQueryParams $params, $data)
    {
        $orderBy  = 'created_at';
        $orderDir = 'desc';
        $page     = ($params->length + $params->start) / $params->length;

        $query = $this->prescription->query()
            ->select([
                'id', 'resource_id', 'user_id', 'visit_id', 'consultation_id', 'created_at', 
                'result_date', 'approved', 'rejected', 'paid', 'discontinued', 'hms_bill', 
                'nhis_bill', 'discontinued_by', 'sample_collected_at', 'sample_collected_by'
            ])
            ->with([
                'resource:id,name,category', 
                'user:id,username', 
                'visit:id,sponsor_id,patient_id',
                'visit.sponsor:id,name,category_name,sponsor_category_id',
                'visit.sponsor.sponsorCategory:id,pay_class',
                'visit.patient:id,first_name,middle_name,last_name,card_no',
                'consultation:id,icd11_diagnosis,provisional_diagnosis,assessment,admission_status',
                'discontinuedBy:id,username',
                'sampleCollectedBy:id,username'
            ])
            // --- 1. THE GATEKEEPERS ---
            ->labInvestigations()
            ->whereNull('result_date')
            ->whereNull('dispense_comment')
            ->whereRelation('visit', 'consulted', '!=', null);

        // --- 2. SEARCH LOGIC ---
        if (!empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $searchTerm    = '%' . addcslashes($searchTermRaw, '%_') . '%';

            $query->whereHas('visit.patient', function (Builder $q) use ($searchTerm, $searchTermRaw) {
                $q->searchByName($searchTermRaw)
                ->orWhere('phone', 'LIKE', $searchTerm)
                ->orWhere('card_no', 'LIKE', $searchTerm);
            });
        } 
        // --- 3. INPATIENT FILTER (Apply if not searching specifically by patient) ---
        else {
            $query->whereHas('visit', fn($q) => $q->inpatientOrObservation());
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, ['*'], 'page', $page);
    }

    // public function getOutpatientLabRequests(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'created_at';
    //     $orderDir   =  'desc';
    //     $query      = $this->prescription->select('id', 'resource_id', 'user_id', 'visit_id', 'consultation_id', 'created_at', 'result_date', 'approved', 'rejected', 'paid', 'discontinued', 'hms_bill', 'nhis_bill', 'discontinued_by')
    //                         ->with([
    //                             'resource:id,name,category', 
    //                             'user:id,username', 
    //                             'visit' => function ($query) {
    //                                 $query->select('id', 'sponsor_id', 'patient_id')
    //                                     ->with([
    //                                     'sponsor' => function ($query){
    //                                         $query->select('id', 'name', 'category_name', 'sponsor_category_id')
    //                                             ->with(['sponsorCategory:id,pay_class']);
    //                                     },
    //                                     'patient:id,first_name,middle_name,last_name,card_no'
    //                                 ]);
    //                             },
    //                             'consultation:id,icd11_diagnosis,provisional_diagnosis,assessment',
    //                             'discontinuedBy:id,username',
    //                         ]);

    //     if (! empty($params->searchTerm)) {
    //         $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
    //         return $query->labInvestigations()
    //                     ->where(function (Builder $query) use($searchTerm) {
    //                         $query->whereRelation('visit.patient', 'first_name', 'LIKE', $searchTerm)
    //                         ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', $searchTerm)
    //                         ->orWhereRelation('visit.patient', 'last_name', 'LIKE', $searchTerm)
    //                         ->orWhereRelation('visit.patient', 'phone', 'LIKE', $searchTerm)
    //                         ->orWhereRelation('visit.patient', 'card_no', 'LIKE', $searchTerm);
    //                         })
    //                     // ->where('discontinued', false)
    //                     ->where('dispense_comment', null)
    //                     ->whereRelation('visit', 'consulted', '!=', null)
    //                     ->orderBy($orderBy, $orderDir)
    //                     ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->notLab){
    //         return $query->labInvestigations()
    //                 ->whereRelation('visit', 'consulted', '!=', null)
    //                 ->whereRelation('consultation', 'admission_status', '=', 'Outpatient')
    //                 ->where('created_at', '>', (new Carbon)->subDays(2))
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     return $query->labInvestigations()
    //                 ->whereRelation('visit', 'consulted', '!=', null)
    //                 ->whereRelation('consultation', 'admission_status', '=', 'Outpatient')
    //                 // ->where('discontinued', false)
    //                 ->where('dispense_comment', null)
    //                 ->where('result_date', null)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    // }

    public function getOutpatientLabRequests(DataTableQueryParams $params, $data)
    {
        $orderBy  = 'created_at';
        $orderDir = 'desc';
        $page     = ($params->length + $params->start) / $params->length;

        $query = $this->prescription->query()
            ->select([
                'id', 'resource_id', 'user_id', 'visit_id', 'consultation_id', 'created_at', 
                'result_date', 'approved', 'rejected', 'paid', 'discontinued', 'hms_bill', 
                'nhis_bill', 'discontinued_by'
            ])
            ->with([
                'resource:id,name,category', 
                'user:id,username', 
                'visit:id,sponsor_id,patient_id',
                'visit.sponsor:id,name,category_name,sponsor_category_id',
                'visit.sponsor.sponsorCategory:id,pay_class',
                'visit.patient:id,first_name,middle_name,last_name,card_no',
                'consultation:id,icd11_diagnosis,provisional_diagnosis,assessment,admission_status',
                'discontinuedBy:id,username',
            ])
            // --- 1. THE GATEKEEPERS (Common to all Outpatient Lab Requests) ---
            ->labInvestigations()
            ->whereRelation('visit', 'consulted', '!=', null)
            ->whereRelation('visit', 'admission_status', 'Outpatient');

        // --- 2. SEARCH LOGIC ---
        if (!empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $searchTerm    = '%' . addcslashes($searchTermRaw, '%_') . '%';

            $query->whereHas('visit.patient', function (Builder $q) use ($searchTerm, $searchTermRaw) {
                $q->searchByName($searchTermRaw)
                ->orWhere('phone', 'LIKE', $searchTerm)
                ->orWhere('card_no', 'LIKE', $searchTerm);
            })
            ->whereNull('dispense_comment');
        } 
        // --- 3. FILTER LOGIC (If not searching) ---
        else {
            if ($data->notLab) {
                // "Not Lab" likely means viewing requests regardless of result status, within a time window
                $query->where('created_at', '>', now()->subDays(2));
            } else {
                // Default view: Pending requests only
                $query->whereNull('result_date')
                    ->whereNull('dispense_comment');
            }
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, ['*'], 'page', $page);
    }

    public function getLabTransformer(): callable
    {
       return  function (Prescription $prescription) {
            return [
                'id'                => $prescription->id,
                'requested'         => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'type'              => $prescription->resource->category,
                'doctor'            => $prescription->user->username,
                'patient'           => $prescription->visit->patient->patientId(),
                'sponsor'           => $prescription->visit->sponsor->name,
                'diagnosis'         => $prescription?->consultation?->icd11_diagnosis ??
                                       $prescription?->consultation?->provisional_diagnosis ??
                                       $prescription?->consultation?->assessment,
                'resource'          => $prescription->resource->name,
                'result'            => $prescription->result_date,
                'sponsorCategory'       => $prescription->visit->sponsor->category_name,
                'sponsorCategoryClass'  => $prescription->visit->sponsor->sponsorCategory->pay_class,
                'approved'          => $prescription->approved,
                'rejected'          => $prescription->rejected,
                'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->category_name == 'NHIS',
                'collected'         => $prescription->sample_collected_at ? (new Carbon($prescription->sample_collected_at))->format('d/m/y g:ia') : null,
                'collectedBy'       => $prescription->sampleCollectedBy?->username,
            ];
         };
    }

    public function createLabResultRecord(Request $data, Prescription $prescription, User $user): Prescription
    {
        return DB::transaction(function () use($data, $prescription, $user) {
    
            $prescription->update([
                'test_sample'    => $data->sample,
                'result'         => $data->result,
                'result_date'    => Carbon::now(),
                'result_by'      => $user->id,
                'discontinued'      => false,
                'dispense_comment'  => null,
                'qty_dispensed'     => 1
                ]);

            // $vPatient = $prescription?->visit?->patient;
            // $wPatient = $prescription?->walkIn;
            // if ($this->helperService->nccTextTime() && !$this->helperService->isAirtel($vPatient?->phone ?? $wPatient?->phone)){
            //     if ($vPatient?->canSms() || $wPatient){
            //         // SendTestResultDone::dispatch($prescription)->delay(5);
            //         ($vPatient ?? $wPatient)->notify(new TestResultNotification($prescription));
            //     }
            // }

            

            $patient = $prescription->visit?->patient ?? $prescription->walkIn;
            $phone   = $patient->phone;

            $recentlySent = function() use ($prescription) {
                            $model = $prescription->visit ?? $prescription->walkIn;

                            // 1. Check if we've already handled a result for this visit/walk-in recently
                            $hasRecentActivity = $model->prescriptions()
                                ->labInvestigations()
                                ->where('id', '!=', $prescription->id) // Ignore the current record
                                ->whereNotNull('result')
                                ->whereBetween('result_date', [now()->subMinutes(30), now()])
                                ->exists();

                            // 2. Logic: If we HAVE recent activity, return FALSE (Don't Notify).
                            // If we DO NOT have recent activity, return TRUE (Okay to Notify).
                            if ($hasRecentActivity) {
                                return false; 
                            }

                            return true; 
                        };

           if ($this->helperService->shouldNotify($phone, $patient, [$recentlySent])) {
                $patient->notify(new TestResultNotification($prescription));
            }
    
            return $prescription;

        }, 2);
    }

    public function updateLabResultRecord(Request $data, Prescription $prescription, User $user): Prescription
    {
        $prescription->update([
                'test_sample'       => $data->sample,
                'result'            => $data->result,
                'result_date'       => Carbon::now(),
                'result_by'         => $user->id,
                'discontinued'      => false,
                'dispense_comment'  => null,
            ]);

        return $prescription;
    }

    public function removeLabResultRecord(Prescription $prescription): Prescription
    {
        $prescription->update([
            'test_sample'       => null,
            'result'            => null,
            'result_date'       => null,
            'result_by'         => null,
            'discontinued'      => false,
            'dispense_comment'  => null,
            'qty_dispensed'     => 0
            ]);

        return  $prescription;
    }

    public function removetTestFromList($data, Prescription $prescription, User $user): Prescription
    {
        $prescription->update([
            // 'discontinued'      => true,
            'discontinued_by'   => $user->id,
            'dispense_comment'  => $data->removalReason,
            ]);

        return  $prescription;
    }

    public function markSampleCollection(Prescription $prescription, User $user)
    {
        return $prescription->update([
            'sample_collected_at'   => Carbon::now(),
            'sample_collected_by'   => $user->id
        ]);
    }

    public function unMarkSampleCollection(Prescription $prescription, User $user)
    {
        return $prescription->update([
            'sample_collected_at'   => null,
            'sample_collected_by'   => $user->id
        ]);
    }

    public function getAllPatientsVisitsTests(Visit $visit)
    {   
            return $this->prescription
                        ->where('visit_id', $visit->id)
                        ->whereRelation('resource', 'category', 'Investigations')
                        // ->whereRelation('resource', 'sub_category', '!=', 'Imaging')
                        ->whereRelation('visit', 'consulted', '!=', null)
                        ->where('result_date', '!=', null)
                        ->orderBy('created_at', 'asc')
                        ->get();
    }
}
