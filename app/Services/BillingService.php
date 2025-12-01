<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Ward;
use App\Models\Visit;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Resource;
use App\Models\Prescription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\DataObjects\DataTableQueryParams;
use Illuminate\Database\Eloquent\Builder;

class BillingService
{
    public function __construct(
        private readonly Visit $visit,
        private readonly Prescription $prescription,
        private readonly Payment $payment,
        private readonly PaymentService $paymentService,
        private readonly PayPercentageService $payPercentageService,
        private readonly Resource $resource,
        private readonly PayMethodService $payMethodService,
        private readonly ExpenseService $expenseService,
        private readonly PrescriptionService $prescriptionService,
        private readonly Patient $patient,
        private readonly Ward $ward,
        private readonly HelperService $helperService
        )
    {
        
    }

    public function getpaginatedFilteredBillingVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';
        $query = $this->visit::with([
            'sponsor.sponsorCategory', 
            'consultations',
            'patient', 
            'prescriptions',
            'doctor', 
            'closedOpenedBy',
            'payments'
        ])
        ->whereNotNull('consulted');

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
                        ->orWhereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', $searchTerm)
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
                        // ->orWhereRelation('patient', 'first_name', 'LIKE', $searchTerm)
                        // ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
                        // ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
                        ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', $searchTerm)
                        ->orWhereRelation('consultations', 'admission_status', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
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
        $latestConsultation = $visit->consultations->sortDesc()->first();
        $ward = $this->ward->where('id', $visit->ward)->first();
            return [
                'id'                => $visit->id,
                'conId'             => $latestConsultation?->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'age'               => $visit->patient->age(),
                'sex'               => $visit->patient->sex,
                'doctor'            => $visit->doctor?->username,
                'diagnosis'         => $latestConsultation?->icd11_diagnosis ?? $latestConsultation?->provisional_diagnosis ?? $latestConsultation?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $ward ? $this->helperService->displayWard($ward) : '',
                'wardId'            => $visit->ward ?? '',
                'wardPresent'       => $ward?->visit_id == $visit->id,
                'visitType'         => $visit->visit_type,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'staff'             => auth()->user()->username,
                'total_bill'        => $visit->total_hms_bill,
                'total_paid'        => $visit->total_paid,
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username
            ];
         };
    }

    public function getPatientBillTable(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return  $this->visit::with([
                        'doctor',  
                        'reminders',
                        'discountBy',
                        'consultations',
                        'payments',
                        'sponsor'  => function ($query) {
                            $query->with([
                                'sponsorCategory',
                                'visits',
                            ]);
                        }, 
                        'patient.visits', 
                        'prescriptions' => function ($query) {
                            $query->with([
                                'thirdPartyServices.thirdParty',
                                'user',
                                'resource' => function ($query) {
                                    $query->with([
                                        'markedFor',
                                        'unitDescription'
                                    ]);
                                },
                                'visit',
                                'approvedBy',
                                'rejectedBy',
                            ]);
                        },
                    ])
                    ->where('id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPatientBillTransformer(): callable
    {
        return  function (Visit $visit) {

        $prescriptions  = $visit->prescriptions;
        $totalHmsBills   = $prescriptions->sum('hms_bill');
        $totalNhisBills = $prescriptions->sum('nhis_bill');
        $determinePayV  = $this->determinePayV($visit);
        $determinePayP  = $this->determinePayP($visit->patient);
        $allDiscountsP  = $this->allDiscountsP($visit->patient);

        $latestConsultation = $visit->consultations->sortDesc()->first();
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
                'diagnosis'             => $latestConsultation?->icd11_diagnosis ?? $latestConsultation?->provisional_diagnosis ?? $latestConsultation?->assessment,
                'came'                  => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'discount'              => $visit->discount ?? '',
                'discountBy'            => $visit->discountBy?->username ?? '',
                'subTotal'              => $totalHmsBills,//totalHmsBills() ?? 0,
                'nhisSubTotal'          => $totalNhisBills,//($visit->totalNhisBills()) ?? 0,
                'nhisNetTotal'          => ($totalNhisBills - $visit->discount)  ?? 0,
                'netTotal'              => $totalHmsBills - $visit->discount,//->totalHmsBills() - $visit->discount,
                'totalPaid'             => $determinePayV ?? 0,
                'balance'               => $totalHmsBills - $visit->discount - $determinePayV ?? 0,
                'nhisBalance'           => $this->sponsorsAllowed($visit->sponsor, ['NHIS']) ? (($totalNhisBills - $visit->discount)) - $determinePayV ?? 0 : 'N/A',
                // 'outstandingPatientBalance'  => $visit->patient->allHmsBills() - $visit->patient->allDiscounts() - $this->determinePayP($visit->patient),
                'outstandingPatientBalance'  => $this->allHmsOrNhisBills($visit->patient) - $allDiscountsP - $determinePayP,
                'outstandingSponsorBalance'  => $this->sponsorsAllowed($visit->sponsor, ['Family', 'Retainership']) ? $this->allHmsBillsS($visit->sponsor) - $this->allDiscountsS($visit->sponsor) - $this->determinePayS($visit->sponsor) : null,
                'outstandingCardNoBalance'   => $this->sponsorsAllowed($visit->sponsor, ['Family', 'Retainership', 'NHIS', 'Individual']) ? $this->sameCardNoOustandings($visit) : null,
                'outstandingNhisBalance'=> $this->sponsorsAllowed($visit->sponsor, ['NHIS']) ? $this->allNhisBillsP($visit->patient) - $allDiscountsP - $determinePayP : null,
                'payMethods'            => $this->payMethodService->list(),
                'notBilled'             => $visit->prescriptions->where('qty_billed', 0)->count(),
                'user'                  => auth()->user()->designation->access_level > 4,
                'reminder'              => $visit->reminders->first(),
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
                    'paid1'             => $prescription->paid,
                    'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->category_name == 'NHIS',
                    'isInvestigation'   => $prescription->resource->category == 'Investigations',
                    'thirdParty'        => $prescription->thirdPartyServices->sortDesc()->first()?->thirdParty->short_name ?? '',
                    'isDischarge'       => $prescription->resource->markedFor?->name === 'discharge',
                ]),
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                
            ];
         };
    }

    public function sponsorsAllowed($sponsor, array $sponsors)
    {
        return in_array($sponsor->category_name, $sponsors);
    }

    public function sameCardNoOustandings(Visit $visit)
    {
        $cardNo = str_split($visit->patient->card_no, 9)[0];
        // $nhis = $visit->sponsor->category_name == 'NHIS';
        if (str_contains($cardNo, 'ANC')){
            return null;
        };
        $patients = $this->patient->where('card_no', 'LIKE', '%' . addcslashes($cardNo, '%_') . '%' )->get();
        $allBills = 0;
        $allDiscounts = 0;
        $allPayments = 0;

        foreach($patients as $patient){
        //    $allBills        += $nhis ? $patient->allNhisBills() : $patient->allHmsBills();
           $allBills        += $this->allHmsOrNhisBills($patient) ;
           $allDiscounts    += $this->allDiscountsP($patient);
           $allPayments     += $this->allPaidPrescriptions($patient) > $this->allPayments($patient) ? $this->allPaidPrescriptions($patient) : $this->allPayments($patient) ;
        //    $allPayments     +=  $patient->allPayments();
        }
        return $allBills - $allDiscounts - $allPayments;
    }

    public function totalNhisBills($prescriptions)
    {
        $totalNhisBills = 0;
        foreach($prescriptions as $prescription) {
            $approved   = $prescription->approved;
            $hmsBill    = $prescription->hms_bill;
                $totalNhisBills += $approved ? $hmsBill/10 : $hmsBill;
            }
        return $totalNhisBills;
    }

    public function determinePayV($visit)
    {
        return $visit->totalPaidPrescriptions() > $visit->totalPayments() ? $visit->totalPaidPrescriptions() : $visit->totalPayments();
        // return $this->totalPaidPrescriptionsV($visit) > $this->totalPaymentsV($visit->payments) ? $this->totalPaidPrescriptionsV($visit) : $this->totalPaymentsV($visit->payments);
        // return $visit->totalPayments();
    }

    public function determinePayS($sponsor)
    {
        // return $this->allPaidPrescriptions($sponsor) > $this->allPayments($sponsor) ? $this->allPaidPrescriptions($sponsor) : $this->allPayments($sponsor);
        return $this->allPaidPrescriptions($sponsor) > $this->allPayments($sponsor) ? $this->allPaidPrescriptions($sponsor) : $this->allPayments($sponsor);
        // return $sponsor->allPayments();
    }

    public function determinePayP($patient)
    {
        return $this->allPaidPrescriptions($patient) > $this->allPayments($patient) ? $this->allPaidPrescriptions($patient) : $this->allPayments($patient);
        // return $patient->allPayments();
    }

    public function allPaidPrescriptions($model)
    {
        $allPayments = 0;
        foreach($model->visits as $visit){
            $allPayments += $visit->totalPaidPrescriptions();
        }

        return $allPayments;
    }
    // public function totalPaidPrescriptionsV($visit)
    // {
    //     $totalPayments = 0;
    //     foreach($visit->prescriptions as $prescription){
    //         $totalPayments += $prescription->paid;
    //     }
        
    //     return $totalPayments;
    // }

    public function totalPaymentsV($payments)
    {
        $totalPayments = 0;
        foreach($payments as $payment){
            $totalPayments += $payment->amount_paid;
        }
        
        return $totalPayments;
    }

    public function totalHmsOrNhisBills($visit)
    {
        $totalBill = 0;
         foreach($visit->prescriptions as $prescription){
            $totalBill += ($visit->sponsor->category_name == 'NHIS' ?  $prescription->nhis_bill : $prescription->hms_bill);
         }

         return $totalBill;
    }

    public function allHmsOrNhisBills($patient)
    {
        $allHmsBills = 0;
        foreach($patient->visits as $visit){
            $allHmsBills += $this->totalHmsOrNhisBills($visit);
        }

        return $allHmsBills;
    }

    public function allDiscountsP($patient)
    {
        $allDiscounts = 0;
        foreach($patient->visits as $visit){
            $allDiscounts += $visit->discount;
        }

        return $allDiscounts;
    }

    public function allDiscountsS($sponsor)
    {
        $allDiscounts = 0;
        foreach($sponsor->visits as $visit){
            $allDiscounts += $visit->discount;
        }

        return $allDiscounts;
    }

    public function allHmsBillsS($sponsor)
    {
        $allHmsBills = 0;
        foreach($sponsor->visits as $visit){
            $allHmsBills += $visit->totalHmsBills();
        }

        return $allHmsBills;
    }

    public function allHmoBillsP($patient)
    {
        $allHmoBills = 0;
        foreach($patient->visits as $visit){
            $allHmoBills += $visit->totalHmoBills();
        }

        return $allHmoBills;
    }

    public function allNhisBillsP($patient)
    {
        $allNhisBills = 0;
        foreach($patient->visits as $visit){
            $allNhisBills += $visit->totalNhisBills();
        }

        return $allNhisBills;
    }

    public function allPayments($model)
    {
        $allPayments = 0;
        foreach($model->visits as $visit){
            $allPayments += $this->totalPaymentsV($visit->payments);
        }

        return $allPayments;
    }

    public function getPatientPaymentTable(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->payment::with(['payMethod', 'user'])
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
        $query = $this->visit::with([
            'sponsor.sponsorCategory', 
            'consultations',
            'patient', 
            'prescriptions',
            'doctor', 
            'closedOpenedBy',
            'payments'
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
        return $query->where('patient_id', $data->patientId)
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
        return DB::transaction(function () use($request, $user) {

            $resources = $this->resource->whereRelation('markedFor', 'id', $request->mark)->get();
            $filteredResources = $resources->reject(function (Object $value) use($request) {
                return $value->sub_category == "Accommodation" && explode(" ", $value->name)[0] !== $request->wardType;
            });

            $filteredResources->map(function ($resource) use($request, $user){
                $this->prescriptionService->createPrescription($request, $resource, $user);
            }); 

        });

        return response();
    }
}
