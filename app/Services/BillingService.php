<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Consultation;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\User;
use App\Models\Visit;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function __construct(
        private readonly Visit $visit,
        private readonly Prescription $prescription,
        private readonly Payment $payment,
        private readonly PaymentService $paymentService,
        private readonly PayPercentageService $payPercentageService,
        private readonly Resource $resource,
        private readonly PayMethodService $payMethodService
        )
    {
        
    }

    public function getpaginatedFilteredBillingVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'consulted';
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
            ->where('closed', false)
            ->where(function (Builder $query){
                $query->whereColumn('total_hms_bill', '>', 'total_paid')
                    ->orWhereColumn('total_nhis_bill', '>', 'total_paid');
            })
            ->where('admission_status', '=', 'Outpatient')
            ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('closed', false)
                    ->where(function (Builder $query){
                        $query->whereColumn('total_hms_bill', '>', 'total_paid')
                            ->whereColumn('total_nhis_bill', '>', 'total_paid');
                    })
                    ->where(function (Builder $query) {
                        $query->where('admission_status', '=', 'Inpatient')
                        ->orWhere('admission_status', '=', 'Observation');
                    })
                    ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
                    ->whereColumn('total_hms_bill', '>', 'total_paid')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('closed', false)
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
                    ->where(function (Builder $query){
                        $query->whereColumn('total_hms_bill', '>', 'total_paid')
                            ->whereColumn('total_nhis_bill', '>', 'total_paid');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('closed', false)
                    ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisitsBillingTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'doctor'            => $visit->doctor->username,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->admission_status,
                'ward'              => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->ward ?? '',
                'bedNo'             => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->bed_no ?? '',
                'patientType'       => $visit->patient->patient_type,
                'sponsorCategory'   => $visit->sponsor->sponsorCategory->name,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'reason'            => $visit->discharge_reason,
                'closed'            => $visit->closed,
                'staff'             => auth()->user()->username,
                'total_bill'        => $visit->total_hms_bill,
                'total_paid'        => $visit->total_paid,
            ];
         };
    }

    public function getPatientBillTable(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->where('id', $data->visitId)
                        ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPatientBillTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                    => $visit->id,
                'visitId'               => $visit->id,
                'patientId'             => $visit->patient->id,
                'patient'               => $visit->patient->patientId(),
                'sponsor'               => $visit->sponsor->name,
                'sponsorCategory'       => $visit->sponsor->sponsorCategory->name,
                'sponsorCategoryClass'  => $visit->sponsor->sponsorCategory->pay_class,
                'doctor'                => $visit->doctor?->username,
                'diagnosis'             => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                           Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                           Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'came'                  => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'discount'              => $visit->discount ?? '',
                'discountBy'            => $visit->discountBy?->username ?? '',
                'subTotal'              => $visit->totalHmsBills() ?? 0,
                'nhisSubTotal'          => ($visit->totalNhisBills()) ?? 0,
                'nhisNetTotal'          => ($visit->totalNhisBills() - $visit->discount)  ?? 0,
                'netTotal'              => $visit->totalHmsBills() - $visit->discount,
                'totalPaid'             => $visit->totalPayments() ?? 0,
                'balance'               => $visit->totalHmsBills() - $visit->discount - $visit->totalPayments() ?? 0,
                'nhisBalance'           => $visit->sponsor->sponsorCategory->name == 'NHIS' ? (($visit->totalNhisBills() - $visit->discount)) - $visit->totalPayments() ?? 0 : 'N/A',
                'outstandingBalance'    => $visit->patient->allHmsBills() - $visit->patient->allDiscounts() - $visit->patient->allPayments(),
                'outstandingNhisBalance'=> $visit->patient->allNhisBills() - $visit->patient->allDiscounts() - $visit->patient->allPayments(),
                'payMethods'            => $this->payMethodService->list(),
                'notBilled'             => $visit->prescriptions->where('qty_billed', null)->count(),
                'prescriptions'         => $visit->prescriptions->map(fn(Prescription $prescription) => [
                    'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                    'prescribedBy'      => $prescription->user->username,
                    'description'       => $prescription->resource->unit_description,
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
                    'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
                ]),
                
            ];
         };
    }

    public function getPatientPaymentTable(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->payment
                        ->where('visit_id', $data->visitId)
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource', 'sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->payment
                    ->where('visit_id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
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
                    'user'          => auth()->user()->designation->access_level > 3
            ];
         };
    }

    public function saveDiscount(Request $request, Visit $visit, User $user): Visit
    {
        $visit->update([
            'total_hms_bill'    => $visit->discount ? ($visit->total_hms_bill + $visit->discount) - $request->discount : $visit->total_hms_bill - $request->discount,
            'total_nhis_bill'   => $visit->discount ? ($visit->total_nhis_bill + $visit->discount) - $request->discount : $visit->total_nhis_bill - $request->discount,
            'discount'          => $request->discount,
            'discount_by'       => $user->id
        ]);

        return $visit;
    }

    public function processPaymentDestroy(Payment $payment)
    {
        return $this->paymentService->destroyPayment($payment);
    }

    public function getVisitsWithOutstandingBills(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->visit
                        ->where('id', $data->patientId)
                        ->orWhereRelation('sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('patient_id', $data->patientId)
                    ->whereColumn('total_hms_bill', '!=', 'total_paid')
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
}