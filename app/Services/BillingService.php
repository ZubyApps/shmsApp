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
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
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

        if ($data->filterBy == 'Outpatient'){
            return $query->where('closed', false)
                    ->where('admission_status', '=', 'Outpatient')
                    ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
                    ->whereRelation('patient', 'patient_type', '!=', 'ANC')
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
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
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
                'patientType'       => $visit->patient->patient_type,
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
                        'sponsor', 
                        'patient', 
                        'prescriptions.thirdPartyServices.thirdParty',
                        'prescriptions.user',
                        'prescriptions.resource',
                        'reminders'
                    ])
                    ->where('id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPatientBillTransformer(): callable
    {
       return  function (Visit $visit) {
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
                'subTotal'              => $visit->totalHmsBills() ?? 0,
                'nhisSubTotal'          => ($visit->totalNhisBills()) ?? 0,
                'nhisNetTotal'          => ($visit->totalNhisBills() - $visit->discount)  ?? 0,
                'netTotal'              => $visit->totalHmsBills() - $visit->discount,
                'totalPaid'             => $this->determinePayV($visit) ?? 0,
                'balance'               => $visit->totalHmsBills() - $visit->discount - $this->determinePayV($visit) ?? 0,
                'nhisBalance'           => $this->sponsorsAllowed($visit->sponsor, ['NHIS']) ? (($visit->totalNhisBills() - $visit->discount)) - $this->determinePayV($visit) ?? 0 : 'N/A',
                // 'outstandingPatientBalance'  => $visit->patient->allHmsBills() - $visit->patient->allDiscounts() - $this->determinePayP($visit->patient),
                'outstandingPatientBalance'  => $visit->patient->allHmsOrNhisBills() - $visit->patient->allDiscounts() - $this->determinePayP($visit->patient),
                'outstandingSponsorBalance'  => $this->sponsorsAllowed($visit->sponsor, ['Family', 'Retainership']) ? $visit->sponsor->allHmsBills() - $visit->sponsor->allDiscounts() - $this->determinePayS($visit->sponsor) : null,
                'outstandingCardNoBalance'   => $this->sponsorsAllowed($visit->sponsor, ['Family', 'Retainership', 'NHIS', 'Individual']) ? $this->sameCardNoOustandings($visit) : null,
                'outstandingNhisBalance'=> $this->sponsorsAllowed($visit->sponsor, ['NHIS']) ? $visit->patient->allNhisBills() - $visit->patient->allDiscounts() - $this->determinePayP($visit->patient) : null,
                'payMethods'            => $this->payMethodService->list(),
                'notBilled'             => $visit->prescriptions->whereNull('qty_billed')->count(),
                'user'                  => auth()->user()->designation->access_level > 4,
                'reminder'              => $visit->reminders->first(),
                'prescriptions'         => $visit->prescriptions->map(fn(Prescription $prescription) => [
                    'prescriptionId'    => $prescription->id,
                    'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                    'prescribedBy'      => $prescription->user->username,
                    'description'       => $prescription->resource->unitDescription?->short_name,
                    'item'              => $prescription->resource->name,
                    'unitPrice'         => $prescription->resource->selling_price,
                    'quantity'          => $prescription->qty_billed ?? '',
                    'hmsBill'           => $prescription->hms_bill ?? '',
                    'nhisBill'          => $prescription->nhis_bill ?? '',
                    'approved'          => $prescription->approved,
                    'rejected'          => $prescription->rejected,
                    'hmoNote'           => $prescription->hmo_note ?? '',
                    'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
                    'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                    'paid1'              => $prescription->paid,
                    'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->category_name == 'NHIS',
                    'isInvestigation'   => $prescription->resource->category == 'Investigations',
                    'thirdParty'        => $prescription->thirdPartyServices->sortDesc()->first()?->thirdParty->short_name ?? ''
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
        $nhis = $visit->sponsor->category_name == 'NHIS';
        if (str_contains($cardNo, 'ANC')){
            return null;
        };
        $patients = $this->patient->where('card_no', 'LIKE', '%' . addcslashes($cardNo, '%_') . '%' )->get();

        $allBills = 0;
        $allDiscounts = 0;
        $allPayments = 0;

        foreach($patients as $patient){
        //    $allBills        += $nhis ? $patient->allNhisBills() : $patient->allHmsBills();
           $allBills        += $patient->allHmsOrNhisBills() ;
           $allDiscounts    += $patient->allDiscounts();
           $allPayments     += $patient->allPaidPrescriptions() > $patient->allPayments() ? $patient->allPaidPrescriptions() : $patient->allPayments() ;
        //    $allPayments     +=  $patient->allPayments();
        }

        return $allBills - $allDiscounts - $allPayments;
    }

    public function determinePayV(Visit $visit)
    {
        return $visit->totalPaidPrescriptions() > $visit->totalPayments() ? $visit->totalPaidPrescriptions() : $visit->totalPayments();
        // return $visit->totalPayments();
    }

    public function determinePayS($sponsor)
    {
        return $sponsor->allPaidPrescriptions() > $sponsor->allPayments() ? $sponsor->allPaidPrescriptions() : $sponsor->allPayments();
        // return $sponsor->allPayments();
    }

    public function determinePayP($patient)
    {
        return $patient->allPaidPrescriptions() > $patient->allPayments() ? $patient->allPaidPrescriptions() : $patient->allPayments();
        // return $patient->allPayments();
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

    public function processPaymentDestroy(Payment $payment)
    {
        return $this->paymentService->destroyPayment($payment);
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
                    ->whereColumn($column, '>', 'total_paid')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        return $query->where('patient_id', $data->patientId)
                    ->whereColumn($column, '>', 'total_paid')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPatientBillSummaryTable($data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return DB::table('prescriptions')
                        ->selectRaw('SUM(prescriptions.hms_bill) as totalBill, SUM(prescriptions.paid) as totalPaid, resources.'.$data->type.' as service, COUNT(resources.category) as types, SUM(prescriptions.qty_billed) as quantity, visits.discount as discount')
                        ->leftJoin('resources', 'prescriptions.resource_id', '=', 'resources.id')
                        ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                        ->where('visit_id', $data->visitId)
                        ->groupBy('service', 'discount')
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
