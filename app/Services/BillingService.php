<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Resource;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Events\BulkPrescriptionsCreated;
use App\DataObjects\DataTableQueryParams;
use Illuminate\Database\Eloquent\Builder;

class BillingService
{
    public function __construct(
        private readonly Visit $visit,
        private readonly Payment $payment,
        private readonly PaymentService $paymentService,
        private readonly PayPercentageService $payPercentageService,
        private readonly Resource $resource,
        private readonly PayMethodService $payMethodService,
        private readonly PrescriptionService $prescriptionService,
        private readonly Patient $patient,
        private readonly HelperService $helperService
        )
    {
        
    }

    public function getpaginatedFilteredBillingVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';
        $query = $this->visit->select('id', 'sponsor_id', 'patient_id', 'doctor_id', 'closed_opened_by', 'admission_status', 'ward', 'bed_no', 'ward_id', 'visit_type', 'discharge_reason', 'doctor_done_at', 'closed', 'closed_opened_by', 'consulted', 'created_at', 'discount')
                    ->with([
                        'sponsor:id,name,category_name,flag', 
                        'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
                        'patient' => function($query){
                                    $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                                    ->with(['flaggedBy:id,username']);
                                },
                        'doctor:id,username', 
                        'closedOpenedBy:id,username',
                        'wards:id,visit_id,short_name,bed_number'
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

                        ->orWhereRelation('patient', 'phone', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm);

                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

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
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'phone', 'LIKE', $searchTerm);
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Outpatient'){
            return $query->where('closed', false)
                    ->where('admission_status', '=', 'Outpatient')
                    ->where('visit_type', '!=', 'ANC')
                    ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
                    ->where(function (Builder $query){
                        $query->where(function (Builder $query){
                            $query->where('total_nhis_bill', '>', 0)
                                ->WhereColumn('total_nhis_bill', '>', 'total_paid');
                                
                                })
                            ->orWhere(function (Builder $query){
                                $query->where('total_nhis_bill', '=', 0)
                                    ->whereColumn('total_hms_bill', '>', 'total_paid');
                                    });
                    })
                    ->whereNotNull('consulted')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $query->where('closed', false)
                    ->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    })
                    ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
                    ->where(function (Builder $query){
                        $query->where(function (Builder $query){
                            $query->where('total_nhis_bill', '>', 0)
                                  ->WhereColumn('total_nhis_bill', '>', 'total_paid');
                                  
                                })
                            ->orWhere(function (Builder $query){
                                $query->where('total_nhis_bill', '=', 0)
                                    ->whereColumn('total_hms_bill', '>', 'total_paid');
                                    });
                    })
                    ->whereNotNull('consulted')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'ANC'){
            return $query->where('closed', false)
                    ->where('visit_type', '=', 'ANC')
                    ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
                    ->where(function (Builder $query){
                        $query->where(function (Builder $query){
                            $query->where('total_nhis_bill', '>', 0)
                                  ->WhereColumn('total_nhis_bill', '>', 'total_paid');
                                  
                                })
                            ->orWhere(function (Builder $query){
                                $query->where('total_nhis_bill', '=', 0)
                                    ->whereColumn('total_hms_bill', '>', 'total_paid');
                                    });
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('closed', false)
                    ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisitsBillingTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'conId'             => $visit->latestConsultation?->id,
                'came'              => (new Carbon($visit->consulted ?? $visit->created_at))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'age'               => $visit->patient->age(),
                'sex'               => $visit->patient->sex,
                'doctor'            => $visit->doctor?->username,
                'diagnosis'         => $visit->latestConsultation?->icd11_diagnosis ?? $visit->latestConsultation?->provisional_diagnosis ?? $visit->latestConsultation?->assessment,
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
                'visitType'         => $visit->visit_type,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'staff'             => auth()->user()->username,
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username
            ];
         };
    }

    public function getPatientBillTable(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return  $this->visit->select('id', 'doctor_id', 'patient_id', 'sponsor_id', 'discount', 'consulted', 'discount_by', 'total_hms_bill', 'total_nhis_bill', 'total_paid')->with([
                        'doctor:id,username',  
                        'discountBy:id,username',
                        'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
                        'sponsor'  => function ($query) {
                            $query->select('id', 'name', 'flag', 'category_name', 'sponsor_category_id' )
                            ->with([
                                'sponsorCategory:id,pay_class',
                            ]);
                        }, 
                        'patient' => function ($query){
                            $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                                ->with(['flaggedBy:id,username'])
                                ->withSum('payments as paymentsSum', 'amount_paid')
                                ->withSum('visits as discountsSum', 'discount');
                        }, 
                        'prescriptions' => function ($query) {
                            $query->select('id', 'visit_id', 'resource_id', 'created_at', 'qty_billed', 'hms_bill', 'nhis_bill', 'approved', 'rejected', 'hmo_note', 'paid', 'approved_by', 'rejected_by', 'user_id')
                                ->with([
                                    'thirdPartyServices' => function ($query) {
                                        $query->select('id', 'third_party_id', 'prescription_id')
                                        ->with(['thirdParty:id,short_name']);
                                    },
                                    'user:id,username',
                                    'resource' => function ($query) {
                                        $query->select('id', 'name', 'category', 'unit_description_id', 'marked_for_id', 'selling_price')
                                        ->with([
                                            'markedFor:id,name',
                                            'unitDescription:id,short_name'
                                        ]);
                                    },
                                    'approvedBy:id,username',
                                    'rejectedBy:id,username',
                                ]);
                        },
                    ])
                    ->withCount([
                        'reminders as remindersCount',
                        'prescriptions as notBilledPrescriptions' => function (Builder $query) {
                                $query->where('qty_billed', 0);
                            },
                        ])
                    ->where('id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPatientBillTransformer(): callable
    {
        return  function (Visit $visit) {
        $allPatientsHmsOrNhisBills = (float)$visit->patient->allHmsOrNhisBills();
            return [
                'id'                    => $visit->id,
                'patientId'             => $visit->patient->id,
                'patient'               => $visit->patient->patientId(),
                'cardNo'                => str_split($visit->patient->card_no, 9)[0],
                'sponsor'               => $visit->sponsor->name,
                'sponsorId'             => $visit->sponsor->id,
                'sponsorCategory'       => $visit->sponsor->category_name,
                'sponsorCategoryClass'  => $visit->sponsor->sponsorCategory->pay_class,
                'doctor'                => $visit->doctor?->username,
                'diagnosis'             => $visit->latestConsultation?->icd11_diagnosis ?? $visit->latestConsultation?->provisional_diagnosis ?? $visit->latestConsultation?->assessment,
                'came'                  => (new Carbon($visit->consulted ?? $visit->created_at))->format('d/m/y g:ia'),
                'discount'              => $visit->discount ?? '',
                'discountBy'            => $visit->discountBy?->username ?? '',
                'subTotal'              => $visit->total_hms_bill,
                'nhisSubTotal'          => $visit->total_nhis_bill,
                'nhisNetTotal'          => ($visit->total_nhis_bill - $visit->discount)  ?? 0,
                'netTotal'              => $visit->total_hms_bill - $visit->discount,
                'totalPaid'             => $visit->total_paid,
                'balance'               => $visit->total_hms_bill - $visit->discount - $visit->total_paid,
                'nhisBalance'           => $this->sponsorsAllowed($visit->sponsor, ['NHIS']) ? (($visit->total_nhis_bill - $visit->discount)) - $visit->total_paid : 'N/A',
                'outstandingPatientBalance'  => $allPatientsHmsOrNhisBills - $visit->patient->discountsSum - $visit->patient->paymentsSum,
                'outstandingSponsorBalance'  => $this->sponsorsAllowed($visit->sponsor, ['Family', 'Retainership']) ? $visit->sponsor->allHmsBills() - $visit->sponsor->allDiscounts() - $this->determinePayS($visit->sponsor) : null,
                'outstandingCardNoBalance'   => $this->sponsorsAllowed($visit->sponsor, ['Family', 'Retainership', 'NHIS', 'Individual']) ? $this->sameCardNoOustandings($visit) : null,
                'outstandingNhisBalance'=> $this->sponsorsAllowed($visit->sponsor, ['NHIS']) ? $allPatientsHmsOrNhisBills - $visit->patient->discountsSum - $visit->patient->paymentsSum : null,
                'payMethods'            => $this->payMethodService->list(),
                'notBilled'             => $visit->notBilledPrescriptions,
                'user'                  => auth()->user()->designation->access_level > 4,
                'reminder'              => $visit->remindersCount,
                'prescriptions'         => $visit->prescriptions->map(fn(Prescription $prescription) => [
                    'prescriptionId'    => $prescription->id,
                    'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                    'prescribedBy'      => $prescription->user->username,
                    'description'       => $prescription->resource->unitDescription?->short_name,
                    'item'              => $prescription->resource->name,
                    'unitPrice'         => $prescription->resource->getSellingPriceForSponsor($visit->sponsor),
                    'quantity'          => $prescription->qty_billed ?? '',
                    'hmsBill'           => $prescription->hms_bill ?? '',
                    'nhisBill'          => $prescription->nhis_bill ?? '',
                    'approved'          => $prescription->approved,
                    'rejected'          => $prescription->rejected,
                    'hmoNote'           => $prescription->hmo_note ?? '',
                    'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
                    'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                    'paid1'              => $prescription->paid,
                    'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $visit->sponsor->category_name == 'NHIS',
                    'isInvestigation'   => $prescription->resource->category == 'Investigations',
                    'thirdParty'        => $prescription->thirdPartyServices->sortDesc()->first()?->thirdParty->short_name ?? '',
                    'isDischarge'       => $prescription->resource->markedFor?->name === 'discharge',
                ]),
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
                
            ];
         };
    }

    public function sponsorsAllowed($sponsor, array $sponsors)
    {
        return in_array($sponsor->category_name, $sponsors);
    }

    public function sameCardNoOustandings(Visit $visit): float
    {
        // 1. Get the base card number fragment
        $cardNoFragment = str_split($visit->patient->card_no, 9)[0];
        $likeCondition = $cardNoFragment . '%';

        // 2. Identify all relevant patient IDs efficiently (1 Query)
        $patientIds = $this->patient
            ->where('card_no', 'LIKE', $likeCondition)
            ->pluck('id');

        if ($patientIds->isEmpty()) {
            return 0.0;
        }
        
        // Get IDs of all relevant visits
        $visitIds = Visit::whereIn('patient_id', $patientIds)->pluck('id');

        if ($visitIds->isEmpty()) {
            return 0.0;
        }

        // --- 3. EXECUTE 3 SEPARATE, HIGHLY TARGETED AGGREGATE QUERIES (3 Queries) ---
        
        // A. Query for Total Bills (Conditional Sum: Hms or Nhis)
        $totalBillsResult = DB::table('prescriptions')
            ->join('visits', 'prescriptions.visit_id', '=', 'visits.id')
            ->join('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->whereIn('prescriptions.visit_id', $visitIds)
            ->sum(DB::raw("
                CASE 
                    WHEN sponsors.category_name = 'NHIS' THEN prescriptions.nhis_bill 
                    ELSE prescriptions.hms_bill 
                END
            ")) ?? 0.0;

        // B. Query for Total Discounts (Direct Sum on Visits)
        $totalDiscountsResult = DB::table('visits')
            ->whereIn('id', $visitIds)
            ->sum('discount') ?? 0.0;

        // C. Query for Total Paid (Prescriptions 'paid' vs. Payments 'amount_paid')
        // C1. Total Paid Prescriptions (Prescription.paid)
        $totalPaidPrescriptions = DB::table('prescriptions')
            ->whereIn('visit_id', $visitIds)
            ->sum('paid') ?? 0.0;

        // C2. Total Payments (Payments.amount_paid)
        $totalPayments = DB::table('payments')
            ->whereIn('visit_id', $visitIds)
            ->sum('amount_paid') ?? 0.0;

        // --- 4. FINAL CALCULATION IN PHP ---
        // var_dump('payments '.$totalPayments, 'prescription '.$totalPaidPrescriptions);
        $allBills = (float)$totalBillsResult;
        $allDiscounts = (float)$totalDiscountsResult;
        $allPaymentsSet = max($totalPaidPrescriptions, $totalPayments);

        // Total Outstanding = Bills - Discounts - Payments
        return $allBills - $allDiscounts - $allPaymentsSet;
    }

    public function determinePayS($sponsor)
    {
        return max($sponsor->allPaidPrescriptions(), $sponsor->allPayments());
    }

    public function getPatientPaymentTable(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->payment->select('id', 'pay_method_id', 'user_id', 'amount_paid', 'comment', 'created_at')
                            ->with(['payMethod:id,name', 'user:id,username'])
                            ->where('visit_id', $data->visitId);

        if (! empty($params->searchTerm)) {
            return $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPatientPaymentTransformer(): callable
    {
       return  function (Payment $payment) {
            return [
                    'id'            => $payment->id,
                    'date'          => (new Carbon($payment->created_at))->format('d/m/y g:ia'),
                    'receivedBy'    => $payment->user->username,
                    'amount'        => $payment->amount_paid,
                    'payMethod'     => $payment->payMethod->name,
                    'comment'       => $payment->comment,
                    'user'          => auth()->user()->designation->access_level > 4
            ];
         };
    }

    public function saveDiscount(Request $request, Visit $visit, User $user): Visit
    {
        return DB::transaction(function () use($request, $visit, $user) {
            $visit->update([
                'total_hms_bill'    => $visit->totalHmsBills(),
                'total_nhis_bill'   => $visit->sponsor->category_name == 'NHIS' ? $visit->totalNhisBills() : 0,
                'discount'          => $request->discount,
                'discount_by'       => $user->id
            ]);
            
            return $visit;
        });

    }

    public function processPaymentDestroy(Payment $payment, bool $softDelete, $data)
    {
        return $this->paymentService->destroyPayment($payment, $softDelete, $data);
    }

    public function getVisitsWithOutstandingBills(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $column = $data->sponsorCat == 'NHIS' ? 'total_nhis_bill' : 'total_hms_bill';
        $query = $this->visit->select('id', 'sponsor_id', 'patient_id', 'doctor_id', 'closed_opened_by', 'admission_status', 'ward', 'ward', 'visit_type', 'discharge_reason', 'doctor_done_at', 'closed', 'closed_opened_by', 'discount')
                    ->with([
                        'sponsor:id,name,category_name,flag', 
                        'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
                        'patient' => function($query){
                                    $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                                    ->with(['flaggedBy:id,username']);
                                }, 
                        'doctor:id,username', 
                        'closedOpenedBy:id,username',
                    ]);
        if ($data->sponsorId){
            if (! empty($params->searchTerm)) {
            return $query->where('sponsor_id', $data->sponsorId)
                        ->where(function (Builder $query) use ($params){
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('sponsor_id', $data->sponsorId)
                    ->whereColumn('total_hms_bill', '!=', 'total_paid')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        
        if ($data->cardNo){
            
            if (! empty($params->searchTerm)) {
            return $query->whereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($data->cardNo, '%_') . '%' )
                        ->where(function (Builder $query) use ($params){
                            $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $query->whereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($data->cardNo, '%_') . '%' )
                    ->whereColumn($column, '!=', 'total_paid')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('patient_id', (int)$data->patientId)
                    ->whereColumn($column, '!=', 'total_paid')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPatientBillSummaryTable($data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return DB::table('prescriptions')
                        ->selectRaw('SUM(prescriptions.hms_bill) as totalBill, SUM(prescriptions.nhis_bill) as totalNhisBill, SUM(prescriptions.paid) as totalPaid, resources.'.$data->type.' as service, COUNT(resources.category) as types, SUM(prescriptions.qty_billed) as quantity, visits.discount as discount, sponsors.category_name as sponsorCat')
                        ->leftJoin('resources', 'prescriptions.resource_id', '=', 'resources.id')
                        ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                        ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                        ->where('visit_id', $data->visitId)
                        ->groupBy('service', 'discount', 'sponsorCat')
                        ->orderBy('service')
                        ->get()
                        ->toArray();   
    }

    public function saveDischargeBill(Request $request, User $user)
    {
        // --- STEP 1: EFFICIENT READS & FILTERING (Remains in controller for quick response) ---
        
        // Get all resources marked for discharge based on the request ID
        $resources = $this->resource->whereRelation('markedFor', 'id', $request->mark)->get();
        
        // PHP Filtering (necessary due to 'Accommodation' logic)
        $filteredResources = $resources->reject(function (Object $value) use($request) {
            return $value->sub_category == "Accommodation" && explode(" ", $value->name)[0] !== $request->wardType;
        });

        $visit = $this->visit->find($request->visitId);

        // --- STEP 2: DATABASE TRANSACTION (Delegated to Service) ---
        $createdCount = DB::transaction(function () use($filteredResources, $request, $user, $visit) {

            if ($filteredResources->isEmpty()) {
                return 0;
            }
            
            // **NEW: Call the unified bulk creation method in the service**
            return $this->prescriptionService->createBulkPrescriptions(
                $filteredResources, 
                $request, 
                $user,
                $visit
            );
        });

        // --- STEP 3: DISPATCH EVENT (Decoupled Recalculation) ---
        if ($createdCount > 0 && $request->visitId) {
            // Dispatch event to handle waterfall, totals, etc. 
            BulkPrescriptionsCreated::dispatch($visit);
        }

        return response()->json(['message' => 'Discharge bill generated successfully', 'count' => $createdCount]);
    }
}
