<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\DataObjects\SponsorCategoryDto;
use App\Events\PrescriptionTreated;
use App\Models\Prescription;
use App\Models\Sponsor;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HmoService
{
    public function __construct(
        private readonly Visit $visit, 
        private readonly Prescription $prescription,
        private readonly PayPercentageService $payPercentageService,
        private readonly Sponsor $sponsor,
        private readonly PaymentService $paymentService,
        )
    {
        
    }

    public function getPaginatedVerificationList(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query = $this->visit
            ->select('id', 'patient_id', 'sponsor_id', 'consulted', 'verification_status', 'verification_code', 'visit_type', 'closed_opened_by', 'doctor_id', 'verified_at', 'verified_by')->with([
                'sponsor:id,name,category_name,flag', 
                'patient' => function($query) {
                    $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no', 'phone', 'sex', 'staff_id')
                    ->with(['flaggedBy:id,username'])
                    ->withCount([
                        'visits as visitsCount' => function (Builder $query) {
                        $query->where('consulted', '>', Carbon::now()->subDays(30));
                        },
                    ]);
                },  
                'doctor:id,username', 
                'closedOpenedBy:id,username',
                'verifiedBy:id,username'
        ]);

        if (! empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $patientId = explode('-', $searchTermRaw)[0] == 'pId' ? explode('-', $searchTermRaw)[1] : null;

            $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

            if ($patientId){ 
                return $query->where('patient_id', $patientId)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            
            return $query->where(function (Builder $query) use($searchTerm) {
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
                            // ->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            // ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'phone', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm);
                        })
                        ->orWhere('verification_status', 'LIKE', $searchTerm)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query
                    ->where('verified_at', null)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('sponsor', 'category_name', '=', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', '=', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', '=', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getVerificationListTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patientId'         => $visit->patient->id,
                'patient'           => $visit->patient->patientId(),
                'staffId'           => $visit->patient->staff_id ?? '',
                'sex'               => $visit->patient->sex,
                'age'               => $visit->patient->age(),
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
                'doctor'            => $visit->doctor?->username ?? '',
                'codeText'          => $visit->verification_code ?? '',
                'phone'             => $visit->patient->phone,
                'status'            => $visit->verification_status ?? '',
                '30dayCount'        => $visit->patient->visitsCount.' visit(s)',
                'visitType'         => $visit->visit_type,
                'verifiedBy'        => $visit->verifiedBy?->username,
                'verifiedAt'        => $visit->verified_at ? (new Carbon($visit->verified_at))->format('d/m/y g:ia') : '',
            ];
         };
    }

    public function verify(Request $request, Visit $visit): Visit
    {
       
            $visit->update([
                'verification_status'   => $request->status,
                'verification_code'     => $request->codeText,
                'verified_at'           => $request->status === 'Verified' || $request->status === 'Exponged' ? new Carbon() : null,
                'verified_by'           => $request->user()->id,
            ]);

            return $visit;
    }

    public function getPaginatedAllConsultedHmoVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';
        $query = $this->visit
        ->select('id', 'patient_id', 'doctor_id', 'sponsor_id', 'consulted', 'admission_status', 'visit_type', 'discharge_reason', 'discharge_remark', 'closed', 'closed_opened_by', 'viewed_at', 'viewed_by', 'hmo_done_at', 'hmo_done_by', 'discount')->with([
            'sponsor:id,name,category_name,flag', 
            'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
            'patient' => function($query) {
                $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no', 'sex', 'staff_id', 'phone')
                ->with(['flaggedBy:id,username'])
                ->withCount([
                    'visits as visitsCount' => function (Builder $query) {
                    $query->where('consulted', '>', Carbon::now()->subDays(30));
                    },
                ]);
            },
            'antenatalRegisteration:id,visit_id', 
            'doctor:id,username', 
            'closedOpenedBy:id,username',
            'viewedBy:id,username',
            'hmoDoneBy:id,username'
        ])
        ->withCount([
            'prescriptions as labPrescribed' => function (Builder $query) {
            $query->whereRelation('resource', 'category', '=', 'Investigations');
            }, 
            'prescriptions as labDone' => function (Builder $query) {
            $query->whereRelation('resource', 'category', '=', 'Investigations')
                ->where('result_date', '!=', null);
            },
            'consultations as consultationsCount'
        ])
        ->whereNotNull('consulted');

        if (! empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $patientId = explode('-', $searchTermRaw)[0] == 'pId' ? explode('-', $searchTermRaw)[1] : null;

            $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

            if ($patientId){ 
                return $query->where('patient_id', $patientId)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where(function (Builder $query) use($searchTerm) {
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
                        ->orWhereRelation('patient', 'phone', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm);
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Outpatient'){
            return $query->where('hmo_done_by', null)
            // ->where('closed', false)
            ->where('admission_status', '=', 'Outpatient')
            ->where('visit_type', '!=', 'ANC')
            ->where(function (Builder $query) {
                $query->whereRelation('sponsor', 'category_name', 'HMO')
                ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                ->orWhereRelation('sponsor', 'category_name', 'Retainership');
            })
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $query->where('hmo_done_by', null)
                    // ->where('closed', false)
                    ->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    })
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $query->where('hmo_done_by', null)
                    // ->where('closed', false)
                    ->where('visit_type', '=', 'ANC')
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('hmo_done_by', null)
                    // ->where('closed', false)
                        ->where(function (Builder $query) {
                            $query->whereRelation('sponsor', 'category_name', 'HMO')
                            ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                            ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                        })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getAllHmoConsultedVisitsTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'age'               => $visit->patient->age(),
                'sex'               => $visit->patient->sex,
                'staffId'           => $visit->patient?->staff_id ?? '',
                'phone'             => $visit->patient?->phone,
                'doctor'            => $visit->doctor?->username,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'diagnosis'         => $visit->latestConsultation?->icd11_diagnosis ?? $visit->latestConsultation?->provisional_diagnosis ?? $visit->latestConsultation?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
                'admissionStatus'   => $visit->admission_status,//latestConsultation?->admission_status,
                'visitType'         => $visit->visit_type,
                'labPrescribed'     => $visit->labPrescribed,
                'labDone'           => $visit->labDone,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'thirtyDayCount'    => $visit->visit_type == 'ANC' ? $visit->consultationsCount : $visit->patient->visitsCount.' visit(s)',
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'viewedAt'          => $visit->viewed_at,
                'viewedBy'          => $visit->viewedBy?->username,
                'hmoDoneBy'         => $visit->hmoDoneBy?->username,
                'sentOn'            => $visit->hmo_done_at ? (new Carbon($visit->hmo_done_at))->format('d/m/y g:ia') : '',
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username

            ];
         };
    }

    public function getPaginatedAllPrescriptionsRequest(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $query      = $this->prescription
            ->select('id', 'visit_id', 'resource_id', 'user_id', 'consultation_id', 'prescription', 'qty_billed', 'note', 'hms_bill', 'hms_bill_date', 'hmo_bill', 'approved', 'rejected', 'qty_dispensed', 'hmo_bill_by', 'approved_by', 'rejected_by', 'created_at')
            ->addSelect(['totalQtyResourceBilled' => function ($query) {
                $query->selectRaw('sum(qty_billed)')
                    ->from('prescriptions as p2')
                    ->whereColumn('p2.resource_id', 'prescriptions.resource_id')
                    ->whereColumn('p2.visit_id', 'prescriptions.visit_id');
            }])
            ->with([
                'visit' => function ($query) {
                    $query->select('id', 'sponsor_id', 'patient_id', 'total_paid')
                    ->with([
                        'sponsor'  => function ($query) {
                                        $query->select('id', 'name', 'category_name', 'flag', 'sponsor_category_id' )
                                        ->with([
                                            'sponsorCategory:id,pay_class',
                                        ]);
                                    },
                        'patient' => function($query){
                            $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                            ->with(['flaggedBy:id,username']);
                        }, 
                    ]);
                },
                'resource:id,flag,name,selling_price',
                'consultation:id,icd11_diagnosis,provisional_diagnosis,assessment',
                'hmoBillBy:id,username',
                'approvedBy:id,username',
                'rejectedBy:id,username',
                'user:id,username',
        ]);

            if (! empty($params->searchTerm)) {

                $searchTermRaw = trim($params->searchTerm);

                $patientId = explode('-', $searchTermRaw)[0] == 'pId' ? explode('-', $searchTermRaw)[1] : null;

                $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

                if ($patientId){ 
                    return $query->whereRelation('visit', 'patient_id', $patientId)
                        ->where(function (Builder $query) use($data) {
                            $query->whereRelation('visit.sponsor', 'category_name', ($data->sponsor == 'NHIS' ? '' : 'HMO'))
                            ->orWhereRelation('visit.sponsor', 'category_name', ($data->sponsor == 'NHIS' ? 'NHIS' : ''))
                            ->orWhereRelation('visit.sponsor', 'category_name', ($data->sponsor == 'NHIS' ? '' : 'Retainership'));
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                return $query->where(function (Builder $query) use($data) {
                        $query->whereRelation('visit.sponsor', 'category_name', ($data->sponsor == 'NHIS' ? '' : 'HMO'))
                        ->orWhereRelation('visit.sponsor', 'category_name', ($data->sponsor == 'NHIS' ? 'NHIS' : ''))
                        ->orWhereRelation('visit.sponsor', 'category_name', ($data->sponsor == 'NHIS' ? '' : 'Retainership'));
                    })
                    ->where(function (Builder $query) use($searchTerm) {
                        $query->whereRelation('visit.sponsor', 'name', 'LIKE', $searchTerm)
                        ->orWhere(function($q) use ($searchTerm) {
                            $terms = array_filter(explode(' ', trim($searchTerm)));
                            foreach ($terms as $term) {
                                $q->where(function($subQuery) use ($term) {
                                    $subQuery->whereRelation('visit.patient', 'first_name', 'LIKE', $term)
                                            ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', $term)
                                            ->orWhereRelation('visit.patient', 'last_name', 'LIKE', $term);
                                });
                            }
                        })
                        ->orWhereRelation('visit.patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('visit.patient', 'phone', 'LIKE', $searchTerm);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->sponsor == 'NHIS'){
                return $query->where('approved', false)
                    ->where('rejected', false)
                    ->whereRelation('visit.sponsor', 'category_name', 'NHIS')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('approved', false)
                    ->where('rejected', false)
                    ->where(function (Builder $query) {
                        $query->whereRelation('visit.sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('visit.sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getAllPrescriptionsTransformer(): callable
    {
       return  function (Prescription $prescription) {
        $sponsorCategory = $prescription->visit?->sponsor->category_name;
        $flag = $prescription->resource->flag;

            return [
                'id'                => $prescription->id,
                'patient'           => $prescription->visit?->patient?->patientId(),
                'age'               => $prescription->visit?->patient?->age(),
                'sponsor'           => $prescription->visit?->sponsor?->name,
                'sponsorCategory'   => $prescription->visit?->sponsor?->category_name,
                'sponsorCategoryClass'   => $prescription->visit?->sponsor?->sponsorCategory?->pay_class,
                'doctor'            => $prescription->user->username,
                'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                'diagnosis'         => $prescription->consultation?->icd11_diagnosis ?? 
                                       $prescription->consultation?->provisional_diagnosis ?? 
                                       $prescription->consultation?->assessment, 
                'resource'          => $prescription->resource->name,
                'resourcePrice'     => $prescription->resource->selling_price,
                'resourceFlagged'   => str_contains($flag, $sponsorCategory ?? '') ? true : false,
                'prescription'      => $prescription->prescription,
                'quantity'          => $prescription->qty_billed,
                'totalQuantity'     => $prescription->totalQtyResourceBilled,//resource->prescriptions->where('visit_id', $prescription->visit->id)->sum('qty_billed'),
                'note'              => $prescription->note,
                'hmsBill'           => $prescription->hms_bill ?? '',
                'hmsBillDate'       => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
                'hmoBill'           => $prescription->hmo_bill ?? '',
                'hmoBillBy'         => $prescription->hmoBillBy?->username,
                'paid'              => $prescription->paid ?? '',
                'approved'          => $prescription->approved,
                'approvedBy'        => $prescription->approvedBy?->username,
                'rejected'          => $prescription->rejected,
                'rejectedBy'        => $prescription->rejectedBy?->username,
                'dispensed'         => $prescription->qty_dispensed,
                'hmoDoneBy'         => $prescription->visit?->hmoDoneBy?->username,
                'flagSponsor'       => $prescription->visit?->sponsor?->flag,
                'flagPatient'       => $prescription->visit?->patient?->flag,
                'flagReason'        => $prescription->visit?->patient?->flag_reason,
                'flaggedBy'         => $prescription->visit?->patient?->flaggedBy?->username,
                'flaggedAt'         => $prescription->visit?->patient?->flagged_at ? (new Carbon($prescription->visit?->patient?->flagged_at))->format('d/m/y g:ia') : '',
            ];
         };
    }

    // public function approve($data, Prescription $prescription, User $user)
    // {
    //     if ($prescription->approved == true || $prescription->rejected == true){
    //         return response('Already treated by ' . $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username, 222);
    //     }

    //     $visit  = $prescription->visit;

    //     $isNhis = $visit->sponsor->category_name == 'NHIS';

    //     return DB::transaction(function () use($data, $prescription, $user, $visit, $isNhis) {

    //         $prescription->update([
    //             'approved'         => true,
    //             'hmo_note'         => $data->note,
    //             'approved_by'      => $user->id,
    //             'approved_rejected_at' => Carbon::now()
    //         ]);


    //         if ($isNhis){
    //             $prescription->update(['nhis_bill' => $prescription->hms_bill/10]);
    //             $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
    //             $visit->update(['total_nhis_bill' => $visit->totalNhisBills()]);
    //         } else {
    //             $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
    //             $visit->update(['total_hms_bill'    => $prescription->visit->totalHmsBills()]);
    //         }

    //         return $prescription;
    //     });
    // }

    public function approve($data, Prescription $prescription, User $user)
    {
        // Use optional chaining for conciseness and single return
        if ($prescription->approved || $prescription->rejected) {
            $approvedBy = $prescription->approvedBy?->username;
            $rejectedBy = $prescription->rejectedBy?->username;
            $username = $approvedBy ?? $rejectedBy;

            // Using HTTP Status 409 Conflict is often better for "already done" logic
            return response('Already treated by ' . ($username ?? 'an unknown user'), 409);
        }

        $visit = $prescription->visit()->with('sponsor')->first();

        $isNhis = $visit->sponsor->category_name == 'NHIS';

        $resourceCat = $prescription->resource->category;

        $isNhisBillable = $resourceCat == 'Medications' || $resourceCat == 'Consumables' ;

        return DB::transaction(function () use($data, $prescription, $user, $visit, $isNhis, $isNhisBillable) {

            // 1. Prepare Prescription Update Data
            $prescriptionUpdates = [
                'approved'             => true,
                'hmo_note'             => $data->note,
                'approved_by'          => $user->id,
                'approved_rejected_at' => Carbon::now(),
            ];

            if ($isNhis){
                // Apply bill adjustment immediately before the main update
                // $prescriptionUpdates['nhis_bill'] = $prescription->hms_bill / 10;
                $prescriptionUpdates['nhis_bill'] = $isNhisBillable ? ($prescription->hms_bill ? $prescription->hms_bill/10 : 0) : 0;
            }
            
            // 3. Perform Single Prescription Update (1 Query)
            $prescription->update($prescriptionUpdates);

            PrescriptionTreated::dispatch($visit, $isNhis);

            return $prescription;
        });
    }

    // public function reject($data, Prescription $prescription, User $user)
    // {
    //     if ($prescription->approved == true || $prescription->rejected == true){
    //         return response('Already treated by ' . $prescription->rejectedBy?->username ??  $prescription->approvedBy?->username, 222);
    //     }

    //     return DB::transaction(function () use($data, $prescription, $user) {

    //         $prescription->update([
    //             'rejected'          => true,
    //             'hmo_note'          => $data->note,
    //             'rejected_by'       => $user->id,
    //             'approved_rejected_at' => Carbon::now()
    //         ]);

    //         $visit  = $prescription->visit;

    //         $isNhis = $visit->sponsor->category_name == 'NHIS';

    //         if ($isNhis){
    //             $prescription->update(['nhis_bill' => $prescription->hms_bill]);
    //             $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
    //             $visit->update(['total_nhis_bill'   => $prescription->visit->totalNhisBills()]);
    //         } else {
    //             $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
    //             $visit->update(['total_hms_bill'    => $prescription->visit->totalHmsBills()]);
    //         }

    //         return $prescription;
    //     });

    // }

    public function reject($data, Prescription $prescription, User $user)
    {
        // Use optional chaining for conciseness and single return
        if ($prescription->approved || $prescription->rejected) {
            $approvedBy = $prescription->approvedBy?->username;
            $rejectedBy = $prescription->rejectedBy?->username;
            $username = $approvedBy ?? $rejectedBy;

            // Using HTTP Status 409 Conflict is often better for "already done" logic
            return response('Already treated by ' . ($username ?? 'an unknown user'), 409);
        }

        $visit = $prescription->visit()->with('sponsor')->first();

        $isNhis = $visit->sponsor->category_name == 'NHIS';

        return DB::transaction(function () use($data, $prescription, $user, $visit, $isNhis) {

            // 1. Prepare Prescription Update Data
            $prescriptionUpdates = [
                'rejected'          => true,
                'hmo_note'          => $data->note,
                'rejected_by'       => $user->id,
                'approved_rejected_at' => Carbon::now()
            ];

            if ($isNhis){
                // Apply bill adjustment immediately before the main update
                $prescriptionUpdates['nhis_bill'] = $prescription->hms_bill;
            }
            
            // 3. Perform Single Prescription Update (1 Query)
            $prescription->update($prescriptionUpdates);

            PrescriptionTreated::dispatch($visit, $isNhis);

            return $prescription;
        });

    }

    // public function reset(Prescription $prescription)
    // {
    //     return DB::transaction(function () use($prescription) {

    //         $prescription->update([
    //             'approved'          => false,
    //             'hmo_note'          => null,
    //             'approved_by'       => null,
    //             'rejected'          => false,
    //             'rejected_by'       => null,
    //         ]);

    //         $visit    = $prescription->visit;

    //         $isNhis = $visit->sponsor->category_name == 'NHIS';

    //         if ($isNhis){
    //             $prescription->update(['nhis_bill' => $prescription->hms_bill]);
    //             $this->paymentService->prescriptionsPaymentSeiveNhis($visit->totalPayments(), $visit->prescriptions);
    //             $visit->update(['total_nhis_bill'   => $prescription->visit->totalNhisBills()]);
    //         } else {
    //             $this->paymentService->prescriptionsPaymentSeive($visit->totalPayments(), $visit->prescriptions);
    //             $visit->update(['total_hms_bill'    => $prescription->visit->totalHmsBills()]);
    //         }
    
    //         return $prescription;
    //     });

    // }

    public function pending($data, Prescription $prescription, User $user)
    {
         // Use optional chaining for conciseness and single return
        if ($prescription->approved || $prescription->rejected) {
            $approvedBy = $prescription->approvedBy?->username;
            $rejectedBy = $prescription->rejectedBy?->username;
            $username = $approvedBy ?? $rejectedBy;

            // Using HTTP Status 409 Conflict is often better for "already done" logic
            return response('Already treated by ' . ($username ?? 'an unknown user'), 409);
        }

        return DB::transaction(function () use($data, $prescription, $user) {

            $prescription->update([
                'hmo_note'          => $data->note,
                'rejected_by'       => $user->id,
                'approved_by'       => $user->id,
            ]);

            return $prescription;
        });

    }

    public function reset(Prescription $prescription)
    {
        $visit = $prescription->visit()->with('sponsor')->first();

        $isNhis = $visit->sponsor->category_name == 'NHIS';

        return DB::transaction(function () use($prescription, $visit, $isNhis) {

            // 1. Prepare Prescription Update Data
            $prescriptionUpdates = [
                'approved'          => false,
                'hmo_note'          => null,
                'approved_by'       => null,
                'rejected'          => false,
                'rejected_by'       => null,
            ];

            if ($isNhis){
                // Apply bill adjustment immediately before the main update
                $prescriptionUpdates['nhis_bill'] = $prescription->hms_bill;
            }
            
            // 3. Perform Single Prescription Update (1 Query)
            $prescription->update($prescriptionUpdates);

            PrescriptionTreated::dispatch($visit, $isNhis);
    
            return $prescription;
        });

    }

    public function getPaginatedVisitPrescriptionsRequest(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->prescription->select('id', 'visit_id', 'resource_id', 'user_id', 'consultation_id', 'prescription', 'qty_billed', 'note', 'hms_bill', 'hmo_bill', 'approved', 'rejected', 'hmo_bill_by', 'approved_by', 'rejected_by', 'created_at', 'paid')->with([ 
            'resource:id,flag,name,selling_price',
            'consultation:id,icd11_diagnosis,provisional_diagnosis,assessment',
            'hmoBillBy:id,username',
            'approvedBy:id,username',
            'rejectedBy:id,username',
            'user:id,username',
            'visit' => function ($query) {
                $query->select('id', 'sponsor_id')
                ->with([
                    'sponsor'  => function ($query) {
                                        $query->select('id', 'category_name', 'sponsor_category_id' )
                                        ->with([
                                            'sponsorCategory:id,pay_class',
                                        ]);
                                    },
                                ]);
            }
        ]);        

            if (! empty($params->searchTerm)) {
                $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
                return $query->where('visit_id', $data->visitId)
                            ->where(function (Builder $query) use($searchTerm) {
                                $query->whereRelation('consultation', 'icd11_diagnosis', 'LIKE', '%' . $searchTerm)
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . $searchTerm)
                                ->orWhereRelation('resource', 'name', 'LIKE', '%' . $searchTerm)
                                ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . $searchTerm)
                                ->orWhereRelation('resource', 'category', 'LIKE', '%' . $searchTerm);
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $query->where('visit_id', $data->visitId)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function saveBill(Request $data, Prescription $prescription, User $user): Prescription
    {
        $prescription->update([
            'hmo_bill'       => $data->bill ?? 0,
            'hmo_bill_date'  => new Carbon(),
            'hmo_bill_by'    => $user->id,
            'hmo_bill_note'  => $data->note
        ]);   
        
        return $prescription;
    }

    public function treat( Visit $visit, User $user)
    {
        return $visit->update([
            'viewed_at' => Carbon::now(),
            'viewed_by' => $user->id,
        ]);
    }

    public function markAsSent( Visit $visit, User $user)
    {
        return $visit->update([
            'hmo_done_by'    => !$visit->hmo_done_by ? $user->id : null,
            'hmo_done_at'    => new Carbon(),
            'total_hmo_bill' => !$visit->hmo_done_by ? $visit->totalHmoBills() : 0,
        ]);
    }

    public function getSentBillsList(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current    = Carbon::now();
        $query = $this->visit->select('id', 'patient_id', 'doctor_id', 'sponsor_id', 'consulted', 'total_hms_bill', 'visit_type', 'total_hmo_bill', 'closed', 'closed_opened_by', 'hmo_done_by', 'hmo_done_at', 'discount')->with([
            'sponsor:id,name,category_name,flag', 
            'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
            'patient' => function($query){
                            $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                            ->with(['flaggedBy:id,username']);
                        }, 
            'doctor:id,username', 
            'closedOpenedBy:id,username',
            'hmoDoneBy:id,username',
        ])
        ->whereNotNull('consulted');

        if (! empty($params->searchTerm)) {

            $searchTermRaw = trim($params->searchTerm);
            $patientId = explode('-', $searchTermRaw)[0] == 'pId' ? explode('-', $searchTermRaw)[1] : null;

            $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';
            if ($data->startDate && $data->endDate){

                if ($patientId){ 
                    return $query->where('patient_id', $patientId)
                    ->whereNotNull('hmo_done_by')
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                return $query->WhereNotNull('hmo_done_by')
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm);
                        })
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->date){
                $date = new Carbon($data->date);
                if ($patientId){ 
                    return $query->where('patient_id', $patientId)
                        ->whereNotNull('hmo_done_by')
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                return $query->WhereNotNull('hmo_done_by')
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm);
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            if ($patientId){ 
                    return $query->where('patient_id', $patientId)
                        ->whereNotNull('hmo_done_by')
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }
            return $query->whereNotNull('hmo_done_by')
                        ->where(function (Builder $query) use($searchTerm) {
                            $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                            ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                            ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                            ->orWhereRelation('hmoDoneBy', 'username', 'LIKE', $searchTerm);
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){

            if ($data->filterByOpen){
                return $query->where('hmo_done_by', '!=', null)
                        ->where('closed', false)
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->where(function (Builder $query) {
                            $query->whereRelation('sponsor', 'category_name', 'HMO')
                            ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                            ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('hmo_done_by', '!=', null)
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->date){
            $date = new Carbon($data->date);

            if ($data->filterByOpen){
                return $query->where('hmo_done_by', '!=', null)
                        ->where('closed', false)
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->where(function (Builder $query) {
                            $query->whereRelation('sponsor', 'category_name', 'HMO')
                            ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                            ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('hmo_done_by', '!=', null)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterByOpen){
            return $query->where('hmo_done_by', '!=', null)
                    ->where('closed', false)
                    ->whereMonth('created_at', $current->month)
                    ->whereYear('created_at', $current->year)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('hmo_done_by', '!=', null)
                    ->whereMonth('created_at', $current->month)
                    ->whereYear('created_at', $current->year)
                    ->where(function (Builder $query) {
                        $query->whereRelation('sponsor', 'category_name', 'HMO')
                        ->orWhereRelation('sponsor', 'category_name', 'NHIS')
                        ->orWhereRelation('sponsor', 'category_name', 'Retainership');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getSentBillsTransformer(): callable
    {
        return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'sponsor'           => $visit->sponsor->name,
                'doctor'            => $visit->doctor?->username,
                'diagnosis'         => $visit->latestConsultation?->icd11_diagnosis ?? $visit->latestConsultation?->provisional_diagnosis ?? $visit->latestConsultation?->assessment,
                'sentBy'            => $visit->hmoDoneBy?->username,
                'sentOn'            => $visit->hmo_done_at ? (new Carbon($visit->hmo_done_at))->format('d/m/y g:ia') : '',
                'totalHmsBill'      => $visit->total_hms_bill,
                'totalHmoBill'      => $visit->total_hmo_bill,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'closed'            => $visit->closed,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
                'visitType'         => $visit->visit_type,
            ];
        };
    }

    public function getReportSummaryTable(DataTableQueryParams $params, $data)
    {
        $current    = Carbon::now();

        if (! empty($params->searchTerm)) {
            return DB::table('visits')
                    ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, MONTHNAME(visits.created_at) as monthName, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, DATE_FORMAT(visits.created_at, "%m") as month, YEAR(visits.created_at) as year, EXTRACT(YEAR_MONTH FROM visits.created_at) as yearMonth')
                    ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                    ->where('consulted', '!=', null)
                    ->where('hmo_done_by', '!=', null)
                    ->where(function (QueryBuilder $query) {
                        $query->where('sponsors.category_name', 'HMO')
                        ->orWhere('sponsors.category_name', 'NHIS' )
                        ->orWhere('sponsors.category_name', 'Retainership' );
                    })
                    ->where(function (QueryBuilder $query) use ($params){
                        $query->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('sponsors.category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    ->groupBy('yearMonth', 'sponsor', 'category', 'monthName', 'year', 'month', 'id')
                    ->orderBy('month')
                    ->get()
                    ->toArray();
        }

        if ($data->category){
            if ($data->startDate && $data->endDate){
                
                return DB::table('visits')
                            ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                            ->where('sponsors.category_name', $data->category)
                            ->where('visits.consulted', '!=', null)
                            ->where('visits.hmo_done_by', '!=', null)
                            ->WhereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                            ->orderBy('sponsor')
                            ->orderBy('visitsCount')
                            ->get()
                            ->toArray();
            }

            if ($data->date){
                $date = new Carbon($data->date);

                return DB::table('visits')
                ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                ->where('sponsors.category_name', $data->category)
                ->where('visits.consulted', '!=', null)
                ->where('visits.hmo_done_by', '!=', null)
                ->whereMonth('visits.created_at', $date->month)
                ->whereYear('visits.created_at', $date->year)
                ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                ->orderBy('sponsor')
                ->orderBy('visitsCount')
                ->get()
                ->toArray();
            }

            return DB::table('visits')
                            ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                            ->where('visits.consulted', '!=', null)
                            ->where('visits.hmo_done_by', '!=', null)
                            ->where('sponsors.category_name', $data->category)
                            ->whereMonth('visits.created_at', $current->month)
                            ->whereYear('visits.created_at', $current->year)
                            ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                            ->orderBy('sponsor')
                            ->orderBy('visitsCount')
                            ->get()
                            ->toArray();
        }

        if ($data->startDate && $data->endDate){
            return DB::table('visits')
                        ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->where('visits.consulted', '!=', null)
                        ->where('visits.hmo_done_by', '!=', null)
                        ->WhereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->where(function (QueryBuilder $query) {
                            $query->where('sponsors.category_name', 'HMO')
                            ->orWhere('sponsors.category_name', 'NHIS' )
                            ->orWhere('sponsors.category_name', 'Retainership' );
                        })
                        ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                        ->orderBy('sponsor')
                        ->orderBy('visitsCount')
                        ->get()
                        ->toArray();
        }

        if ($data->date){
            $date = new Carbon($data->date);

            return DB::table('visits')
            ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->where('visits.consulted', '!=', null)
            ->where('visits.hmo_done_by', '!=', null)
            ->whereMonth('visits.created_at', $date->month)
            ->whereYear('visits.created_at', $date->year)
            ->where(function (QueryBuilder $query) {
                $query->where('sponsors.category_name', 'HMO')
                ->orWhere('sponsors.category_name', 'NHIS' )
                ->orWhere('sponsors.category_name', 'Retainership' );
            })
            ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
            ->orderBy('sponsor')
            ->orderBy('visitsCount')
            ->get()
            ->toArray();
        }

        return DB::table('visits')
                        ->selectRaw('SUM(visits.total_hms_bill) as totalHmsBill, SUM(visits.total_hmo_bill) as totalHmoBill, SUM(visits.total_paid) as totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, sponsors.name as sponsor, sponsors.id as id, sponsors.category_name as category, COUNT(DISTINCT visits.id) as visitsCount, SUM(CASE WHEN visits.hmo_done_by IS NOT NULL THEN 1 ELSE 0 END) AS billsSent, MONTHNAME(visits.created_at) as monthName, YEAR(visits.created_at) as year')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->where('visits.consulted', '!=', null)
                        ->where('visits.hmo_done_by', '!=', null)
                        ->whereMonth('visits.created_at', $current->month)
                        ->whereYear('visits.created_at', $current->year)
                        ->where(function (QueryBuilder $query) {
                            $query->where('sponsors.category_name', 'HMO')
                            ->orWhere('sponsors.category_name', 'NHIS' )
                            ->orWhere('sponsors.category_name', 'Retainership' );
                        })
                        ->groupBy('sponsor', 'id', 'category', 'monthName', 'year')
                        ->orderBy('sponsor')
                        ->orderBy('visitsCount')
                        ->get()
                        ->toArray();   
    }

    public function getReportSummaryTable1(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'name';
        $orderDir   =  'asc';
        $getDate     = (new Carbon($data->date ?? $data->startDate));
        $baseQuery  = $this->sponsor->select('id','name', 'category_name', 'flag');

        $visitConstraintsWithoutDate = function (Builder $query) use ($getDate) {
            $query->whereMonth('created_at', $getDate->month)
                  ->whereYear('created_at', $getDate->year)
                  ->whereNotNull('consulted')
                  ->whereNotNull('hmo_done_by');
        };

        function reminderQueryFunction($assignedDate){
            return function($query) use($assignedDate){
                    $query->select('id', 'sponsor_id', 'amount_confirmed', 'confirmed_paid', 'first_reminder', 'second_reminder', 'final_reminder', 'month_sent_for')
                    ->whereMonth('month_sent_for', $assignedDate->month)
                    ->whereYear('month_sent_for', $assignedDate->year);
                };
        }

        if (! empty($params->searchTerm)) {
            
            $query = $baseQuery->with([
                'reminders' => reminderQueryFunction($getDate)
            ])
            ->withCount([
                'visits as visitsCount' => $visitConstraintsWithoutDate,
                'visits as billsSent' => $visitConstraintsWithoutDate,
            ])
            ->withSum(['visits as totalHmsBill' => $visitConstraintsWithoutDate], 'total_hms_bill')
            ->withSum(['visits as totalHmoBill' => $visitConstraintsWithoutDate], 'total_hmo_bill')
            ->withSum(['visits as nhisBill' => $visitConstraintsWithoutDate], 'total_nhis_bill')
            ->withSum(['visits as totalPaid' => $visitConstraintsWithoutDate], 'total_paid')
            ->withSum(['visits as totalCapitation' => $visitConstraintsWithoutDate], 'total_capitation')
            ->withSum(['visits as discount' => $visitConstraintsWithoutDate], 'discount');

            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use ($searchTerm){
                    $query->where('name', 'LIKE', $searchTerm)
                    ->orWhere('category_name', 'LIKE', $searchTerm);
                })
                ->hmoDeptCategories()
                ->whereHas('visits', $visitConstraintsWithoutDate)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->category){
            if ($data->startDate && $data->endDate){

                $visitConstraintsRange = function (Builder $query) use ($data) {
                    $query->WhereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                          ->whereNotNull('consulted')
                          ->whereNotNull('hmo_done_by');
                };

                $query = $baseQuery->with([
                    'reminders' => reminderQueryFunction($data->startDate)
                ])
                ->withCount([
                    'visits as visitsCount' => $visitConstraintsRange,
                    'visits as billsSent' => $visitConstraintsRange,
                ])
                ->withSum(['visits as totalHmsBill' => $visitConstraintsRange], 'total_hms_bill')
                ->withSum(['visits as totalHmoBill' => $visitConstraintsRange], 'total_hmo_bill')
                ->withSum(['visits as nhisBill' => $visitConstraintsRange], 'total_nhis_bill')
                ->withSum(['visits as totalPaid' => $visitConstraintsRange], 'total_paid')
                ->withSum(['visits as totalCapitation' => $visitConstraintsRange], 'total_capitation')
                ->withSum(['visits as discount' => $visitConstraintsRange], 'discount');
    
                return $query->where('category_name', $data->category)
                        ->whereHas('visits', $visitConstraintsRange)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
    
            if ($data->date){
                $date   = new Carbon($data->date);

                $visitConstraints = function (Builder $query) use ($date) {
                    $query->whereMonth('created_at', $date->month)
                          ->whereYear('created_at', $date->year)
                          ->whereNotNull('consulted')
                          ->whereNotNull('hmo_done_by');
                };

                $query  = $baseQuery->with([
                            'reminders' => reminderQueryFunction($date)
                    ])
                    ->withCount([
                        'visits as visitsCount' => $visitConstraints,
                        'visits as billsSent' => $visitConstraints,
                    ])
                    ->withSum(['visits as totalHmsBill' => $visitConstraints], 'total_hms_bill')
                    ->withSum(['visits as totalHmoBill' => $visitConstraints], 'total_hmo_bill')
                    ->withSum(['visits as nhisBill' => $visitConstraints], 'total_nhis_bill')
                    ->withSum(['visits as totalPaid' => $visitConstraints], 'total_paid')
                    ->withSum(['visits as totalCapitation' => $visitConstraints], 'total_capitation')
                    ->withSum(['visits as discount' => $visitConstraints], 'discount');
        
                    return $query->where('category_name', $data->category)
                            ->whereHas('visits', $visitConstraints)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            
            $query  = $baseQuery->with([
                        'reminders' => reminderQueryFunction($getDate)
                    ])
                    ->withCount([
                        'visits as visitsCount' => $visitConstraintsWithoutDate,
                        'visits as billsSent' => $visitConstraintsWithoutDate,
                    ])
                    ->withSum(['visits as totalHmsBill' => $visitConstraintsWithoutDate], 'total_hms_bill')
                    ->withSum(['visits as totalHmoBill' => $visitConstraintsWithoutDate], 'total_hmo_bill')
                    ->withSum(['visits as nhisBill' => $visitConstraintsWithoutDate], 'total_nhis_bill')
                    ->withSum(['visits as totalPaid' => $visitConstraintsWithoutDate], 'total_paid')
                    ->withSum(['visits as totalCapitation' => $visitConstraintsWithoutDate], 'total_capitation')
                    ->withSum(['visits as discount' => $visitConstraintsWithoutDate], 'discount');

            return $query->where('category_name', $data->category)
                        ->whereHas('visits', $visitConstraintsWithoutDate)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){

            $startDate = new Carbon($data->startDate);
            $visitConstraintsRange = function (Builder $query) use ($data) {
                $query->WhereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                      ->whereNotNull('consulted')
                      ->whereNotNull('hmo_done_by');
            };

            $query = $baseQuery->with([
                'reminders' => reminderQueryFunction($startDate)
            ])
            ->withCount([
                'visits as visitsCount' => $visitConstraintsRange,
                'visits as billsSent' => $visitConstraintsRange,
            ])
            ->withSum(['visits as totalHmsBill' => $visitConstraintsRange], 'total_hms_bill')
            ->withSum(['visits as totalHmoBill' => $visitConstraintsRange], 'total_hmo_bill')
            ->withSum(['visits as nhisBill' => $visitConstraintsRange], 'total_nhis_bill')
            ->withSum(['visits as totalPaid' => $visitConstraintsRange], 'total_paid')
            ->withSum(['visits as totalCapitation' => $visitConstraintsRange], 'total_capitation')
            ->withSum(['visits as discount' => $visitConstraintsRange], 'discount');

            return $query->hmoDeptCategories()
                    ->whereHas('visits', $visitConstraintsRange)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->date){
            $date = new Carbon($data->date);

            $visitConstraints = function (Builder $query) use ($date) {
                $query->whereMonth('created_at', $date->month)
                      ->whereYear('created_at', $date->year)
                      ->whereNotNull('consulted')
                      ->whereNotNull('hmo_done_by');
            };
           
            $query  = $baseQuery->with([
                'reminders' => reminderQueryFunction($date)
            ])
            ->withCount([
                'visits as visitsCount' => $visitConstraints,
                'visits as billsSent' => $visitConstraints,
            ])
            ->withSum(['visits as totalHmsBill' => $visitConstraints], 'total_hms_bill')
            ->withSum(['visits as totalHmoBill' => $visitConstraints], 'total_hmo_bill')
            ->withSum(['visits as nhisBill' => $visitConstraints], 'total_nhis_bill')
            ->withSum(['visits as totalPaid' => $visitConstraints], 'total_paid')
            ->withSum(['visits as totalCapitation' => $visitConstraints], 'total_capitation')
            ->withSum(['visits as discount' => $visitConstraints], 'discount');

            return $query->hmoDeptCategories()
                    ->whereHas('visits', $visitConstraints)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        $query  = $baseQuery->with([
            'reminders' => reminderQueryFunction($getDate)
        ])
        ->withCount([
            'visits as visitsCount' => $visitConstraintsWithoutDate,
            'visits as billsSent' => $visitConstraintsWithoutDate,
        ])
        ->withSum(['visits as totalHmsBill' => $visitConstraintsWithoutDate], 'total_hms_bill')
        ->withSum(['visits as totalHmoBill' => $visitConstraintsWithoutDate], 'total_hmo_bill')
        ->withSum(['visits as nhisBill' => $visitConstraintsWithoutDate], 'total_nhis_bill')
        ->withSum(['visits as totalPaid' => $visitConstraintsWithoutDate], 'total_paid')
        ->withSum(['visits as totalCapitation' => $visitConstraintsWithoutDate], 'total_capitation')
        ->withSum(['visits as discount' => $visitConstraintsWithoutDate], 'discount');

        return $query->hmoDeptCategories()
                    ->whereHas('visits', $visitConstraintsWithoutDate)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));                        
    }

    public function getReportsSummaryTransformer($data)
    {
        return function (Sponsor $sponsor) use ($data){
            $monthName  = (new Carbon($data->date ?? $data->startDate))->monthName;
            $year       = (new Carbon($data->date ?? $data->startDate))->year;
            $monthYear  = (new Carbon($data->date ?? $data->startDate))->format('F Y');
            return [
                'id'                => $sponsor->id,
                'sponsor'           => $sponsor->name,
                'category'          => $sponsor->category_name,
                'visitsCount'       => $sponsor->visitsCount,
                'billsSent'         => $sponsor->billsSent,
                'totalHmsBill'      => $sponsor->totalHmsBill,
                'totalHmoBill'      => $sponsor->totalHmoBill,
                'nhisBill'          => $sponsor->nhisBill,
                'totalPaid'         => $sponsor->totalPaid,
                'totalCapitation'   => $sponsor->totalCapitation,
                'discount'          => $sponsor->discount,
                'reminderSet'       => $this->updatableReminderDisplay($sponsor->reminders->first(), $sponsor->name),
                'monthYear'         => $monthYear,
                'monthName'         => $monthName,
                'year'              => $year,
                'flagSponsor'       => $sponsor->flag,
            ];
        };
    }

    public function updatableReminderDisplay($reminder, $sponsorName)
    {
        return $reminder ? ($reminder?->confirmed_paid ? '<i class="ms-1 text-primary bi bi-p-circle-fill tooltip-test" title="paid"></i>' . (new Carbon($reminder->confirmed_paid))->format('d/m/y') . ' - ' . number_format((float)$reminder->amount_confirmed) : null) ?? ($reminder?->final_reminder ? $this->reportTableHtmlFormat('Final reminder', $reminder ,'final_reminder', $sponsorName) : null ) ?? ($reminder?->second_reminder ? $this->reportTableHtmlFormat('Second reminder', $reminder ,'second_reminder', $sponsorName) : null) ?? ($reminder?->first_reminder ? $this->reportTableHtmlFormat('First reminder', $reminder ,'first_reminder', $sponsorName) : null) ?? $this->reportTableHtmlFormat('Bill Sent', $reminder, null, $sponsorName) : null;
    }

    public function monthYearCoverter($date)
    {
        return (new Carbon($date))->format('F Y');
    }

    public function reportTableHtmlFormat($text, $reminder, $type, $sponsorName){
        if ($type){
            return '<span class="confirmedPaidBtn" data-id="' . $reminder->id .'" data-sponsor="'. $sponsorName .'" data-monthYear="'. $this->monthYearCoverter($reminder->month_sent_for) .'">'. $text .' - '. $type.'</span>';
        }
        return '<span class="confirmedPaidBtn" data-id="'. $reminder->id .'" data-sponsor="'. $sponsorName . '" data-monthYear="' . $this->monthYearCoverter($reminder->month_sent_for) .'">'. $text .'</span>';
    }

    public function getVisitsForReconciliation(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $query = $this->visit
        ->select('id', 'patient_id', 'doctor_id', 'sponsor_id', 'created_at', 'total_hms_bill', 'total_hmo_bill', 'total_nhis_bill', 'total_capitation', 'total_paid', 'closed')->with([
            'sponsor'  => function ($query) {
                    $query->select('id', 'sponsor_category_id', 'name', 'category_name')
                    ->with([
                        'sponsorCategory:id,pay_class', 
                    ]);
                }, 
            'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
            'patient' => function($query){
                    $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                    ->with(['flaggedBy:id,username']);
                }, 
            'prescriptions' => function ($query) {
                $query->select('id', 'visit_id', 'resource_id', 'user_id', 'consultation_id', 'prescription', 'qty_billed', 'note', 'hms_bill', 'nhis_bill', 'hmo_bill', 'approved', 'rejected', 'capitation', 'approved_by', 'rejected_by', 'paid')
                ->with([
                    'resource:id,name',
                    'approvedBy:id,username',
                    'rejectedBy:id,username',
                ]);
            },
            'doctor:id,username', 
        ])
        ->where('sponsor_id', $data->sponsorId)
        ->whereNotNull('consulted');

            if (! empty($params->searchTerm)) {
                $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
                if ($data->from && $data->to){
                    return $query->where(function (Builder $query) use($searchTerm) {
                                $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm )
                                ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                                ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', $searchTerm)
                                ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', $searchTerm);
                            })
                            ->WhereBetween('created_at', [$data->from.' 00:00:00', $data->to.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                if ($data->date){
                    $date = new Carbon($data->date);
                    return $query->where(function (Builder $query) use($searchTerm) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                        ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', $searchTerm)
                        ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', $searchTerm);
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }

                return $query->where(function (Builder $query) use($searchTerm) {
                                $query->whereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                                ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                                ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', $searchTerm)
                                ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', $searchTerm);
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->from && $data->to){
                return $query->WhereBetween('created_at', [$data->from.' 00:00:00', $data->to.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->date){
                $date = new Carbon($data->date);

                return $query->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $query->where('consulted', '!=', null)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisitsForReconciliationTransformer(): callable
    {
       return  function (Visit $visit) {
            // $visit->update(['total_capitation' => $visit->totalPrescriptionCapitations()]);
            return [
                'id'                    => $visit->id,
                'came'                  => (new Carbon($visit->created_at))->format('D d/m/y g:ia'),                
                'patient'               => $visit->patient->patientId(),
                'consultBy'             => $visit->doctor->username,
                'diagnosis'             => $visit->latestConsultation?->icd11_diagnosis ?? $visit->latestConsultation?->provisional_diagnosis ?? $visit->latestConsultation?->assessment, 
                'sponsorCategory'       => $visit->sponsor->category_name,
                'sponsorCategoryClass'  => $visit->sponsor->sponsorCategory->pay_class,
                'closed'                => $visit->closed,
                'totalHmsBill'          => $visit->total_hms_bill,
                'totalHmoBill'          => $visit->total_hmo_bill,
                'totalNhisBill'         => $visit->total_nhis_bill,
                'totalCapitation'       => $visit->total_capitation,
                'totalPaid'             => $visit->total_paid,
                'prescriptions'         => $visit->prescriptions->map(fn(Prescription $prescription)=> [
                    'id'                => $prescription->id ?? '',
                    'prescribed'        => (new Carbon($prescription->created_at))->format('D d/m/y g:ia') ?? '',
                    'item'              => $prescription->resource->name,
                    'prescription'      => $prescription->prescription ?? '',
                    'qtyBilled'         => $prescription->qty_billed,
                    // 'unit'              => $prescription->resource->unitDescription?->short_name,
                    'hmoBill'           => $prescription->hmo_bill ?? '',
                    'hmsBill'           => $prescription->hms_bill ?? '',
                    'nhisBill'          => $prescription->nhis_bill ?? '',
                    'capitation'        => $prescription->capitation ?? '',
                    'approved'          => $prescription->approved, 
                    'rejected'          => $prescription->rejected,
                    'hmoNote'           => $prescription->hmo_note ?? '',
                    'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
                    'note'              => $prescription->note ?? '',
                    'status'            => $prescription->status ?? '',
                    // 'paidNhis'          => $prescription->paid > 0 && $prescription->paid >= $prescription->nhis_bill && $visit->sponsor->category_name == 'NHIS',
                    'paid'              => $prescription->paid ?? '',
                ]),
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
            ];
         };
    }

    // public function savePayment(Request $data, Prescription $prescription, User $user)
    // {
    //     return DB::transaction(function () use($data, $prescription, $user) {

    //         $prescription->update([
    //             'paid'      => $data->amountPaid ?? 0,
    //             'paid_by'   => $user->id,
    //             'paid_at'   => Carbon::now()
    //         ]);

    //         $visit = $prescription->visit;

    //         $visit->total_paid = $visit->totalPaidPrescriptions();
    //         $visit->save();

    //         return $prescription;
    //     });
    // }

    // public function savePayment(Request $data, Prescription $prescription, User $user)
    // {
    //     // --- STEP 1: PRE-TRANSACTION READS ---
    //     // 1. Eager load the visit now, to use its methods later.
    //     // NOTE: This is a read outside the transaction.
    //     $visit = $prescription->visit;

    //     // Prepare update data
    //     $paidAmount = (float)($data->amountPaid ?? 0);
    //     $now = Carbon::now();
    //     $userId = $user->id;

    //     if (!$visit) {
    //         // Handle case where prescription is not linked to a visit
    //         return response('Prescription not associated with a visit.', 404);
    //     }

    //     // --- STEP 2: DATABASE TRANSACTION (Atomic Writes) ---

    //     return DB::transaction(function () use($prescription, $visit, $paidAmount, $userId, $now) {

    //         // 1. Update the Prescription (1 Query) - ATOMIC WRITE
    //         $prescription->update([
    //             'paid'      => $paidAmount,
    //             'paid_by'   => $userId,
    //             'paid_at'   => $now,
    //         ]);

    //         // 2. Recalculate and Update Visit Totals (1 Query) - DEPENDENT WRITE
    //         // This line runs your confirmed efficient aggregate query (SUM)
    //         $newTotalPaid = $visit->totalPaidPrescriptions(); 

    //         // Update the visit's total_paid field
    //         $visit->update([
    //             'total_paid' => $newTotalPaid
    //         ]);
    //         // Note: Using $visit->update(['total_paid' => ...]) is generally preferred 
    //         // over $visit->total_paid = ...; $visit->save(); as it's a single, immediate update.

    //         return $prescription;
    //     });
    // }
    
    public function savePayment(Request $data, Prescription $prescription, User $user)
    {
        // --- STEP 1: PRE-TRANSACTION READS ---

        // 1. Get the necessary Visit ID outside the transaction for context.
        // This assumes the prescription's visit_id is already set.
        $visitId = $prescription->visit_id;

        // Prepare update data
        $paidAmount = (float)($data->amountPaid ?? 0);
        $now = Carbon::now();
        $userId = $user->id;

        // --- STEP 2: DATABASE TRANSACTION (Atomic Writes) ---

        return DB::transaction(function () use($prescription, $visitId, $paidAmount, $userId, $now) {

            // 1. Update the Prescription (1 Query)
            $prescription->update([
                'paid'      => $paidAmount,
                'paid_by'   => $userId,
                'paid_at'   => $now,
            ]);

            // 2. Recalculate and Update Visit Totals (1 Single, Efficient Query)
            // This query:
            // a) Sums the 'paid' column of ALL prescriptions for the visit ID.
            // b) Updates the 'total_paid' column on the Visit table with the new sum.
            
            // Ensure the table name 'visits' is correct for your setup
            DB::table('visits')
                ->where('id', $visitId)
                ->update([
                    'total_paid' => DB::raw("(SELECT SUM(paid) FROM prescriptions WHERE visit_id = {$visitId})"),
                ]);
                
            // NOTE: The second argument [$visitId] binds the value to the placeholder (?)
            // inside the raw SQL subquery.

            // You could also execute $visit->totalPaidPrescriptions() here if it's an efficient raw query.
            // If totalPaidPrescriptions() is just an aggregate query, this raw DB::update is the most efficient way to run it.

            return $prescription;
        });
    }

    public function saveBulkPayment(Request $data, Visit $visit)
    {
        return DB::transaction(function () use($data, $visit) {

            if ($visit->sponsor->category_name == 'Retainership'){
                $dto = new SponsorCategoryDto(isRetainership: true);
                // $this->paymentService->prescriptionsPaymentSeiveRetanership((float)$data->bulkPayment, $prescriptions);
            } else {
                $dto = new SponsorCategoryDto(isHmo: true);
                // $this->paymentService->prescriptionsPaymentSeiveHmo((float)$data->bulkPayment, $prescriptions);
            }

            $this->paymentService->applyPaymentsWaterfall($visit, (float)$data->bulkPayment, $dto);
                

            $visit->update(['total_paid' => $visit->totalPaidPrescriptions()]);

            return $visit;
        });
    }

    public function determineValueOfTotalPaid(Visit $visit)
    {
        return $visit->totalPaidPrescriptions();
    }

    // public function getNhisSponsorsByDate(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'name';
    //     $orderDir   =  'asc';
    //     $current    = CarbonImmutable::now();
    //     $searchDate = $data->date ? (new Carbon($data->date)) : null;

    //     if (! empty($params->searchTerm)) {
    //         if ($searchDate){
    //             return $this->sponsor
    //                     ->where('category_name', 'NHIS')
    //                     ->whereHas('visits', function(Builder $query) use($searchDate){
    //                         $query->whereMonth('created_at', $searchDate->month)
    //                                 ->whereYear('created_at', $searchDate->year);
    //                     })
    //                     ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                     ->orderBy($orderBy, $orderDir)
    //                     ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }
    //         return $this->sponsor
    //                     ->where('category_name', 'NHIS')
    //                     ->whereHas('visits', function(Builder $query) use($current){
    //                         $query->whereMonth('created_at', $current->month)
    //                               ->whereYear('created_at', $current->year);
    //                     })
    //                     ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                     ->orderBy($orderBy, $orderDir)
    //                     ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($searchDate){
    //         return $this->sponsor
    //                 ->where('category_name', 'NHIS')
    //                 ->whereHas('visits', function(Builder $query) use($searchDate){
    //                     $query->whereMonth('created_at', $searchDate->month)
    //                             ->whereYear('created_at', $searchDate->year);
    //                 })
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }
    //     return $this->sponsor
    //                 ->where('category_name', 'NHIS')
    //                 ->whereHas('visits', function(Builder $query) use($current){
    //                     $query->whereMonth('created_at', $current->month)
    //                           ->whereYear('created_at', $current->year);
    //                 })
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));                        
    // }

    // public function getSponsorsByDateTransformer($data)
    // {
    //     return function (Sponsor $sponsor) use ($data){
    //         $month      = (new Carbon($data->date))->month;
    //         $year       = (new Carbon($data->date))->year;
    //         $monthYear  = (new Carbon($data->date))->format('F Y');
    //         return [
    //             'id'                => $sponsor->id,
    //             'sponsor'           => $sponsor->name,
    //             'category'          => $sponsor->category_name,
    //             'patientsR'         => $sponsor->patients->count(),
    //             'patientsC'         => $sponsor->patients()->whereHas('visits', fn(Builder $query)=>$query->whereMonth('created_at', $month))->count(),
    //             'visitsC'           => $sponsor->visits()->whereMonth('created_at', $month)->count(),
    //             'visitsP'           => $sponsor->visits()->whereHas('prescriptions', fn(Builder $query)=>$query->whereMonth('created_at', $month))->count(),
    //             'prescriptions'     => $sponsor->through('visits')->has('prescriptions')->whereMonth('prescriptions.created_at', $month)->count(),
    //             'hmsBill'           => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_hms_bill'),
    //             'nhisBill'          => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_nhis_bill'),
    //             'paid'              => $sponsor->visits()->whereMonth('created_at', $month)->sum('total_paid'),
    //             'capitationPayment' => $sponsor->capitationPayments()->whereMonth('month_paid_for', $month)->whereYear('month_paid_for', $year)->first()?->amount_paid,
    //             'monthYear'         => $monthYear,
    //         ];
    //     };
    // }

    public function getNhisSponsorsByDate(DataTableQueryParams $params, $data)
    {
        $orderBy = 'name';
        $orderDir = 'asc';
        
        // Determine the relevant date for filtering
        $searchDate = $data->date ? new Carbon($data->date) : CarbonImmutable::now(); 
        
        // Base query filtered by category
        $baseQuery = $this->sponsor->select('id', 'name', 'category_name')
                        ->where('category_name', 'NHIS')
                        ->with([
                            'visits' => function($query) use ($searchDate) {
                                $query->select('id', 'sponsor_id')
                                ->whereMonth('created_at', $searchDate->month)
                                ->whereYear('created_at', $searchDate->year)
                                ->withCount([
                                    'prescriptions as prescriptionsCount'
                                ]);
                            }
                        ]);

        // Reusable date constraint for the 'visits' relationship
        $visitDateConstraint = function (Builder $query) use ($searchDate) {
            $query->whereMonth('created_at', $searchDate->month)
                ->whereYear('created_at', $searchDate->year);
        };

        // 1. Filter the main sponsors list to only include those with visits in the period
        // $query = $baseQuery->whereHas('visits', $visitDateConstraint);
        
        // 2. Build the main query with all aggregates (withCount, withSum, with)
        $query = $baseQuery->withCount([
            // visitsC: Total visits count for the month
            'visits as visits_c_count' => $visitDateConstraint,
            
            // visitsP: Visits with prescriptions (visits that have at least one prescription)
            'visits as visits_p_count' => function (Builder $query) use ($visitDateConstraint) {
                $visitDateConstraint($query);
                $query->whereHas('prescriptions');
            },
            
            // **NEW:** Total number of prescriptions written during the visit period
            'visits as total_prescriptions_count' => function (Builder $query) use ($visitDateConstraint) {
                $query->whereHas('prescriptions', $visitDateConstraint);
            },
            
            // patientsR: Registered patients (total count of related patients, no date constraint)
            'patients as patients_r_count',

            // patientsC: Unique patients count for the month (patients who had a visit this month)
            'patients as patients_c_count' => function (Builder $query) use ($visitDateConstraint) {
                $query->whereHas('visits', $visitDateConstraint);
            }
        ])
        ->withSum(['visits as hms_bill_sum' => $visitDateConstraint], 'total_hms_bill')
        ->withSum(['visits as nhis_bill_sum' => $visitDateConstraint], 'total_nhis_bill')
        ->withSum(['visits as paid_sum' => $visitDateConstraint], 'total_paid')
        
        // Eager load Capitation Payment for easy access in transformer
        ->with(['capitationPayments' => function ($query) use ($searchDate) {
            $query->select('id', 'sponsor_id', 'amount_paid')
                ->whereMonth('month_paid_for', $searchDate->month)
                ->whereYear('month_paid_for', $searchDate->year);
        }]);

        // 3. Apply Search Term (Conditional)
        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            $query->where('name', 'LIKE', $searchTerm);
        }

        // 4. Finalize and Paginate
        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getSponsorsByDateTransformer($data)
    {
        $monthYear = (new Carbon($data->date))->format('F Y');

        return function (Sponsor $sponsor) use ($monthYear) {
            $capitation = $sponsor->capitationPayments->first();
            return [
                'id'                  => $sponsor->id,
                'sponsor'             => $sponsor->name,
                'category'            => $sponsor->category_name,
                
                // Accessing pre-calculated counts
                'patientsR'           => $sponsor->patients_r_count,
                'patientsC'           => $sponsor->patients_c_count,
                'visitsC'             => $sponsor->visits_c_count,
                'visitsP'             => $sponsor->visits_p_count,
                // 'prescriptions'       => $sponsor->total_prescriptions_count, // <<--- New Attribute
                'prescriptions'      => $sponsor->visits->sum('prescriptionsCount'), // <<--- Need a better approach later
                
                // Accessing pre-calculated sums
                'hmsBill'             => $sponsor->hms_bill_sum,
                'nhisBill'            => $sponsor->nhis_bill_sum,
                'paid'                => $sponsor->paid_sum,
                
                'capitationPayment'   => $capitation ? $capitation->amount_paid : 0,
                'monthYear'           => $monthYear,
            ];
        };
    }

    public function totalYearlyIncomeFromHmoPatients($data)
    {
        $currentDate = new Carbon();
        if ($data->year){

            return DB::table('reminders')
                            ->selectRaw('SUM(amount_confirmed) as paidHmo, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                            ->whereNull('visit_id')
                            ->whereYear('created_at', $data->year)
                            ->groupBy('month_name', 'month')
                            ->orderBy('month')
                            ->get();
        }

        return DB::table('reminders')
                        ->selectRaw('SUM(amount_confirmed) as paidHmo, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                        ->whereNull('visit_id')
                        ->whereYear('created_at', $currentDate->year)
                        ->groupBy('month_name', 'month')
                        ->orderBy('month')
                        ->get();
    }
}