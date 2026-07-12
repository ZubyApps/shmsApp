<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Events\BulkPrescriptionsCreated;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\User;
use App\Models\Visit;
use App\Services\PaymentService;
use App\Services\PayPercentageService;
use Carbon\Carbon;
// use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        private readonly HelperService $helperService,
        private readonly TotalsService $totalsService
        )
    {
        
    }

    // public function getpaginatedFilteredBillingVisits(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'consulted';
    //     $orderDir   =  'desc';
    //     $query = $this->visit->select('id', 'sponsor_id', 'patient_id', 'doctor_id', 'closed_opened_by', 'doctor_done_by', 'closed_opened_at', 'admission_status', 'ward', 'bed_no', 'ward_id', 'visit_type', 'discharge_reason', 'doctor_done_at', 'closed', 'closed_opened_by', 'closed_opened_at', 'consulted', 'created_at', 'discount')
    //                 ->with([
    //                     'sponsor:id,name,category_name,flag', 
    //                     'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
    //                     'patient' => function($query){
    //                                 $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
    //                                 ->with(['flaggedBy:id,username']);
    //                             },
    //                     'doctor:id,username', 
    //                     'closedOpenedBy:id,username',
    //                     'wards:id,visit_id,short_name,bed_number',
    //                     'doctorDoneBy:id,username',
    //                 ]);

    //     if (! empty($params->searchTerm)) {
    //         $searchTermRaw = trim($params->searchTerm);
    //         $patientId = explode('-', $searchTermRaw)[0] == 'pId' ? explode('-', $searchTermRaw)[1] : null;

    //         $searchTerm = '%' . addcslashes($searchTermRaw, '%_') . '%';

    //         if ($data->filterBy == 'ANC'){
    //             $query->where('visit_type', '=', 'ANC');

    //             if ($patientId){ 
    //                 return $query->where('patient_id', $patientId)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //             }

    //             return $query
    //                 // ->where('visit_type', '=', 'ANC')
    //                 ->where(function (Builder $query) use($searchTerm) {
    //                     $query->where('created_at', 'LIKE', $searchTerm)
    //                     ->orWhere(function($q) use ($searchTerm) {
    //                         $terms = array_filter(explode(' ', trim($searchTerm)));
    //                         foreach ($terms as $term) {
    //                             $q->where(function($subQuery) use ($term) {
    //                                 $subQuery->whereRelation('patient', 'first_name', 'LIKE', $term)
    //                                         ->orWhereRelation('patient', 'middle_name', 'LIKE', $term)
    //                                         ->orWhereRelation('patient', 'last_name', 'LIKE', $term);
    //                             });
    //                         }
    //                     })

    //                     ->orWhereRelation('patient', 'phone', 'LIKE', $searchTerm)
    //                     ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm);

    //                 })
                    
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //         if ($patientId){ 
    //             return $query->where('patient_id', $patientId)
    //                         ->orderBy($orderBy, $orderDir)
    //                         ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //         return $query->where(function (Builder $query) use($searchTerm) {
    //                     $query->where('created_at', 'LIKE', $searchTerm)
    //                     ->orWhere(function($q) use ($searchTerm) {
    //                         $terms = array_filter(explode(' ', trim($searchTerm)));
    //                         foreach ($terms as $term) {
    //                             $q->where(function($subQuery) use ($term) {
    //                                 $subQuery->whereRelation('patient', 'first_name', 'LIKE', $term)
    //                                         ->orWhereRelation('patient', 'middle_name', 'LIKE', $term)
    //                                         ->orWhereRelation('patient', 'last_name', 'LIKE', $term);
    //                             });
    //                         }
    //                     })
    //                     ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
    //                     ->orWhereRelation('patient', 'phone', 'LIKE', $searchTerm);
    //                 })
                    
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->filterBy == 'Outpatient'){
    //         return $query->where('closed', false)
    //                 ->where('admission_status', '=', 'Outpatient')
    //                 ->where('visit_type', '!=', 'ANC')
    //                 ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
    //                 ->where(function (Builder $query){
    //                     $query->where(function (Builder $query){
    //                         $query->where('total_nhis_bill', '>', 0)
    //                             ->WhereColumn('total_nhis_bill', '>', 'total_paid');
                                
    //                             })
    //                         ->orWhere(function (Builder $query){
    //                             $query->where('total_nhis_bill', '=', 0)
    //                                 ->whereColumn('total_hms_bill', '>', 'total_paid');
    //                                 });
    //                 })
    //                 ->whereNotNull('consulted')
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->filterBy == 'Inpatient'){
    //         return $query->where('closed', false)
    //                 ->where(function (Builder $query) {
    //                     $query->where('admission_status', '=', 'Inpatient')
    //                     ->orWhere('admission_status', '=', 'Observation');
    //                 })
    //                 ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
    //                 ->where(function (Builder $query){
    //                     $query->where(function (Builder $query){
    //                         $query->where('total_nhis_bill', '>', 0)
    //                               ->WhereColumn('total_nhis_bill', '>', 'total_paid');
                                  
    //                             })
    //                         ->orWhere(function (Builder $query){
    //                             $query->where('total_nhis_bill', '=', 0)
    //                                 ->whereColumn('total_hms_bill', '>', 'total_paid');
    //                                 });
    //                 })
    //                 ->whereNotNull('consulted')
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->filterBy == 'ANC'){
    //         return $query->where('closed', false)
    //                 ->where('visit_type', '=', 'ANC')
    //                 ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
    //                 ->where(function (Builder $query){
    //                     $query->where(function (Builder $query){
    //                         $query->where('total_nhis_bill', '>', 0)
    //                               ->WhereColumn('total_nhis_bill', '>', 'total_paid');
                                  
    //                             })
    //                         ->orWhere(function (Builder $query){
    //                             $query->where('total_nhis_bill', '=', 0)
    //                                 ->whereColumn('total_hms_bill', '>', 'total_paid');
    //                                 });
    //                 })
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     return $query->where('closed', false)
    //                 ->whereRelation('sponsor.sponsorCategory', 'pay_class', '=', 'Cash')
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    // }

    public function getpaginatedFilteredBillingVisits(DataTableQueryParams $params, $data)
    {
        $orderBy  = 'consulted';
        $orderDir = 'desc';
        $page     = ($params->length + $params->start) / $params->length;

        $query = $this->visit->query()
            ->select('id', 'sponsor_id', 'patient_id', 'doctor_id', 'closed_opened_by', 'doctor_done_by', 'closed_opened_at', 'admission_status', 'ward', 'bed_no', 'ward_id', 'visit_type', 'discharge_reason', 'doctor_done_at', 'closed', 'consulted', 'created_at', 'discount', 'total_hms_bill', 'total_nhis_bill', 'total_paid')
            ->with([
                'sponsor:id,name,category_name,flag', 
                'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
                'patient' => fn($q) => $q->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                                        ->with('flaggedBy:id,username'),
                'doctor:id,username', 
                'closedOpenedBy:id,username',
                'wards:id,visit_id,short_name,bed_number',
                'doctorDoneBy:id,username',
            ]);

        // 1. Handle Search Term logic (DRY)
        if (!empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $searchTerm    = addcslashes($searchTermRaw, '%_') . '%';

            // Apply ANC filter if needed during search
            if ($data->filterBy == 'ANC') {
                $query->where('visit_type', 'ANC');
            }

            if (str_starts_with($searchTermRaw, 'pId-')) {
                $query->where('patient_id', explode('-', $searchTermRaw)[1]);
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $searchTermRaw)) {
            $query->whereBetween('created_at', [$searchTermRaw . ' 00:00:00', $searchTermRaw . ' 23:59:59']);
            }   else {
                // $query->where(function ($q) use ($searchTerm, $searchTermRaw) {
                //     $q->where('created_at', 'LIKE', $searchTerm)
                //     ->orWhereRelation('patient', 'phone', 'LIKE', $searchTerm)
                //     ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
                //     ->orWhereHas('patient', fn($pq) => $pq->searchByName($searchTermRaw))
                //     // D. Sponsor Group (Single Subquery)
                //     ->orWhereHas('sponsor', function ($q) use ($searchTerm) {
                //         $q->where('name', 'LIKE', $searchTerm)
                //         ->orWhere('category_name', 'LIKE', $searchTerm);
                //     });
                // });
                $query->where(function ($sub) use ($searchTerm, $searchTermRaw) {
                    $sub->whereHas('patient', function ($q) use ($searchTerm, $searchTermRaw) {
                        $q->where(function ($patientSub) use ($searchTerm, $searchTermRaw) {
                            $patientSub->searchByName($searchTermRaw)
                                    ->orWhere('card_no', 'LIKE', $searchTerm)
                                    ->orWhere('phone', 'LIKE', $searchTerm);
                        });
                    })
                    ->orWhereHas('sponsor', function ($q) use ($searchTerm) {
                        $q->where('name', 'LIKE', $searchTerm)
                        ->orWhere('category_name', 'LIKE', $searchTerm);
                    });
                });
            }
            return $query->orderBy($orderBy, $orderDir)->paginate($params->length, ['*'], 'page', $page);
        }

        // 2. Base Filter for all billing views
        $query->where('closed', false)
            ->whereRelation('sponsor.sponsorCategory', 'pay_class', 'Cash');

        // 3. Apply Specific Billing Logic (The "Debt" calculation)
        $applyDebtFilter = function($q) {
            $q->whereNotNull('consulted')
            ->where(function ($sub) {
                $sub->where(fn($n) => $n->where('total_nhis_bill', '>', 0)->whereColumn('total_nhis_bill', '>', 'total_paid'))
                    ->orWhere(fn($h) => $h->where('total_nhis_bill', 0)->whereColumn('total_hms_bill', '>', 'total_paid'));
            });
        };

        // 4. Filter By logic
        switch ($data->filterBy) {
            case 'Outpatient':
                $query->where('admission_status', 'Outpatient')
                    ->where('visit_type', '!=', 'ANC')
                    ->tap($applyDebtFilter);
                break;

            case 'Inpatient':
                $query->whereIn('admission_status', ['Inpatient', 'Observation'])
                    ->tap($applyDebtFilter);
                break;

            case 'ANC':
                $query->where('visit_type', 'ANC')
                    ->tap($applyDebtFilter);
                break;
        }

        return $query->orderBy($orderBy, $orderDir)->paginate($params->length, ['*'], 'page', $page);
    }

    public function getVisitsBillingTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'conId'             => $visit->latestConsultation?->id,
                'came'              => (new Carbon($visit->consulted ?? $visit->created_at))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'patientId'           => $visit->patient_id,
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
                'doctorDone'        => $visit->doctorDoneBy->username ?? '',
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'staff'             => auth()->user()->username,
                'closed'            => $visit->closed,
                'closedOpenedBy'    => $visit->closedOpenedBy?->username,
                'closedOpenedAt'    => $visit->closed_opened_at ? (new Carbon($visit->closed_opened_at))->format('d/m/y g:ia') : '',
            ];
         };
    }

    // public function getPatientBillTable(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'created_at';
    //     $orderDir   =  'desc';

    //     return  $this->visit->select('id', 'doctor_id', 'patient_id', 'sponsor_id', 'discount', 'consulted', 'discount_by', 'total_hms_bill', 'total_nhis_bill', 'total_paid')->with([
    //                     'doctor:id,username',  
    //                     'discountBy:id,username',
    //                     'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
    //                     'sponsor'  => function ($query) {
    //                         $query->select('id', 'name', 'flag', 'category_name', 'sponsor_category_id' )
    //                         ->with([
    //                             'sponsorCategory:id,pay_class',
    //                         ]);
    //                     }, 
    //                     'patient' => function ($query){
    //                         $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
    //                             ->with(['flaggedBy:id,username'])
    //                             ->withSum('payments as paymentsSum', 'amount_paid')
    //                             ->withSum('visits as discountsSum', 'discount');
    //                     }, 
    //                     'prescriptions' => function ($query) {
    //                         $query->select('id', 'visit_id', 'resource_id', 'created_at', 'qty_billed', 'hms_bill', 'nhis_bill', 'approved', 'rejected', 'hmo_note', 'paid', 'approved_by', 'rejected_by', 'user_id')
    //                             ->with([
    //                                 'thirdPartyServices' => function ($query) {
    //                                     $query->select('id', 'third_party_id', 'prescription_id')
    //                                     ->with(['thirdParty:id,short_name']);
    //                                 },
    //                                 'user:id,username',
    //                                 'resource' => function ($query) {
    //                                     $query->select('id', 'name', 'category', 'unit_description_id', 'marked_for_id', 'selling_price')
    //                                     ->with([
    //                                         'markedFor:id,name',
    //                                         'unitDescription:id,short_name'
    //                                     ]);
    //                                 },
    //                                 'approvedBy:id,username',
    //                                 'rejectedBy:id,username',
    //                             ]);
    //                     },
    //                 ])
    //                 ->withCount([
    //                     'reminders as remindersCount',
    //                     'prescriptions as notBilledPrescriptions' => function (Builder $query) {
    //                             $query->where('qty_billed', 0);
    //                         },
    //                     ])
    //                 ->where('id', $data->visitId)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    // }

    public function getPatientBillTable(DataTableQueryParams $params, $data)
    {
        $orderBy  = 'created_at';
        $orderDir = 'desc';
        $page     = ($params->length + $params->start) / $params->length;
        $visitId = (int)$data->visitId;

        $sponsorId = $this->visit->where('id', $visitId)->value('sponsor_id');

        return $this->visit->query()
            ->select([
                'id', 'doctor_id', 'patient_id', 'sponsor_id', 
                'discount', 'consulted', 'discount_by', 
                'total_hms_bill', 'total_nhis_bill', 'total_paid', 'created_at'
            ])
            ->with([
                'doctor:id,username',  
                'discountBy:id,username',
                'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
                'sponsor' => fn($q) => $q->select('id', 'name', 'flag', 'category_name', 'sponsor_category_id', 'total_bill', 'total_discount', 'total_paid')
                    ->with('sponsorCategory:id,pay_class'),
                
                'patient' => fn($q) => $q->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no', 'total_bill', 'total_discount', 'total_paid' )
                    ->with('flaggedBy:id,username'),
                    // ->withSum('payments as paymentsSum', 'amount_paid')
                    // ->withSum('visits as discountsSum', 'discount'),

                'prescriptions' => fn($q) => $q->select([
                        'id', 'visit_id', 'resource_id', 'created_at', 'qty_billed', 
                        'hms_bill', 'nhis_bill', 'approved', 'rejected', 'hmo_note', 
                        'paid', 'approved_by', 'rejected_by', 'user_id'
                    ])
                    ->orderBy('created_at', 'asc')
                    ->with([
                        'user:id,username',
                        'approvedBy:id,username',
                        'rejectedBy:id,username',
                        'thirdPartyServices.thirdParty:id,short_name',
                        'resource' => function($rq) use ($sponsorId) {
                        $rq->select('id', 'name', 'category', 'unit_description_id', 'marked_for_id', 'selling_price')
                            ->with([
                                    'markedFor:id,name',
                                    'unitDescription:id,short_name',
                                    'sponsors' => fn($sq) => $sq->select('sponsors.id')->where('sponsor_id', $sponsorId)->withPivot('selling_price')
                            ]);
                        },
                    ]),
            ])
            ->withExists([
                'reminders as hasReminder',
                'prescriptions as hasUnbilledPrescriptions' => fn($q) => $q->where('qty_billed', 0),
            ])
            ->where('id', $visitId)
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, ['*'], 'page', $page);
    }

    public function getPatientBillTransformer(): callable
    {
        $payMethods = $this->payMethodService->list();
        $accessLevel = auth()->user()->designation->access_level ?? 0;
        return  function (Visit $visit) use ($payMethods, $accessLevel) {

            $sponsor = $visit->sponsor;
            $patient = $visit->patient;

            return [
                'id'                    => $visit->id,
                'patientId'             => $patient->id,
                'patient'               => $patient->patientId(),
                'cardNo'                => str_split($patient->card_no, 9)[0],
                'sponsor'               => $sponsor->name,
                'sponsorId'             => $sponsor->id,
                'sponsorCategory'       => $sponsor->category_name,
                'sponsorCategoryClass'  => $sponsor->sponsorCategory->pay_class,
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
                'nhisBalance'           => $this->sponsorsAllowed($sponsor, ['NHIS']) ? (($visit->total_nhis_bill - $visit->discount)) - $visit->total_paid : 'N/A',
                'outstandingPatientBalance'  => $patient->total_bill - $patient->total_discount - $patient->total_paid,
                'outstandingSponsorBalance'  => $this->sponsorsAllowed($sponsor, ['Family', 'Retainership']) ? $sponsor->total_bill - $sponsor->total_discount - $sponsor->total_paid : null,
                'outstandingCardNoBalance'   => $this->sponsorsAllowed($sponsor, ['Family', 'Retainership', 'NHIS', 'Individual']) ? $this->sameCardNoOustandings($visit) : null,
                'outstandingNhisBalance'=> $this->sponsorsAllowed($sponsor, ['NHIS']) ? $patient->total_bill - $patient->total_discount - $patient->total_paid : null,
                'payMethods'            => $payMethods,
                'notBilled'             => $visit->hasUnbilledPrescriptions,
                'user'                  => $accessLevel > 4,
                'reminder'              => $visit->hasReminder,
                'prescriptions'         => $visit->prescriptions->map(fn(Prescription $prescription) => [
                    'prescriptionId'    => $prescription->id,
                    'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia'),
                    'prescribedBy'      => $prescription->user->username,
                    'description'       => $prescription->resource->unitDescription?->short_name,
                    'item'              => $prescription->resource->name,
                    'unitPrice'         => $prescription->resource->getSellingPriceForSponsor($sponsor),
                    'quantity'          => $prescription->qty_billed ?? '',
                    'hmsBill'           => $prescription->hms_bill ?? '',
                    'nhisBill'          => $prescription->nhis_bill ?? '',
                    'approved'          => $prescription->approved,
                    'rejected'          => $prescription->rejected,
                    'hmoNote'           => $prescription->hmo_note ?? '',
                    'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
                    'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                    'paid1'              => $prescription->paid,
                    'paidNhis'          => $prescription->paid > 0 && $prescription->approved && $prescription->paid >= $prescription->nhis_bill && $sponsor->category_name == 'NHIS',
                    'isInvestigation'   => $prescription->resource->category == 'Investigations',
                    'thirdParty'        => $prescription->thirdPartyServices->sortDesc()->first()?->thirdParty->short_name ?? '',
                    'isDischarge'       => $prescription->resource->markedFor?->name === 'discharge',
                ]),
                'flagSponsor'       => $sponsor->flag,
                'flagPatient'       => $patient->flag,
                'flagReason'        => $patient?->flag_reason,
                'flaggedBy'         => $patient->flaggedBy?->username,
                'flaggedAt'         => $patient->flagged_at ? (new Carbon($patient->flagged_at))->format('d/m/y g:ia') : '',
                
            ];
         };
    }

    public function sponsorsAllowed($sponsor, array $sponsors)
    {
        return in_array($sponsor->category_name, $sponsors);
    }

    // public function sameCardNoOustandings(Visit $visit): float
    // {
    //     // 1. Get the base card number fragment
    //     $cardNoFragment = str_split($visit->patient->card_no, 9)[0];
    //     $likeCondition = $cardNoFragment . '%';

    //     // 2. Identify all relevant patient IDs efficiently (1 Query)
    //     $patientIds = $this->patient
    //         ->where('card_no', 'LIKE', $likeCondition)
    //         ->pluck('id');

    //     if ($patientIds->isEmpty()) {
    //         return 0.0;
    //     }
        
    //     // Get IDs of all relevant visits
    //     $visitIds = Visit::whereIn('patient_id', $patientIds)->pluck('id');

    //     if ($visitIds->isEmpty()) {
    //         return 0.0;
    //     }

    //     // --- 3. EXECUTE 3 SEPARATE, HIGHLY TARGETED AGGREGATE QUERIES (3 Queries) ---
        
    //     // A. Query for Total Bills (Conditional Sum: Hms or Nhis)
    //     $totalBillsResult = DB::table('prescriptions')
    //         ->join('visits', 'prescriptions.visit_id', '=', 'visits.id')
    //         ->join('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
    //         ->whereIn('prescriptions.visit_id', $visitIds)
    //         ->sum(DB::raw("
    //             CASE 
    //                 WHEN sponsors.category_name = 'NHIS' THEN prescriptions.nhis_bill 
    //                 ELSE prescriptions.hms_bill 
    //             END
    //         ")) ?? 0.0;

    //     // B. Query for Total Discounts (Direct Sum on Visits)
    //     $totalDiscountsResult = DB::table('visits')
    //         ->whereIn('id', $visitIds)
    //         ->sum('discount') ?? 0.0;

    //     // C. Query for Total Paid (Prescriptions 'paid' vs. Payments 'amount_paid')
    //     // C1. Total Paid Prescriptions (Prescription.paid)
    //     $totalPaidPrescriptions = DB::table('prescriptions')
    //         ->whereIn('visit_id', $visitIds)
    //         ->sum('paid') ?? 0.0;

    //     // C2. Total Payments (Payments.amount_paid)
    //     $totalPayments = DB::table('payments')
    //         ->whereIn('visit_id', $visitIds)
    //         ->sum('amount_paid') ?? 0.0;

    //     // --- 4. FINAL CALCULATION IN PHP ---
    //     // var_dump('payments '.$totalPayments, 'prescription '.$totalPaidPrescriptions);
    //     $allBills = (float)$totalBillsResult;
    //     $allDiscounts = (float)$totalDiscountsResult;
    //     $allPaymentsSet = max($totalPaidPrescriptions, $totalPayments);

    //     // Total Outstanding = Bills - Discounts - Payments
    //     return $allBills - $allDiscounts - $allPaymentsSet;
    // }

    public function sameCardNoOustandings(Visit $visit): float
    {
        $cardNoFragment = str_split($visit->patient->card_no, 9)[0] . '%';

        // Instead of summing visits/prescriptions, just sum the Patient totals!
        $totals = DB::table('patients')
            ->where('card_no', 'LIKE', $cardNoFragment)
            ->selectRaw('
                SUM(total_bill) as total_bills,
                SUM(total_discount) as total_discounts,
                SUM(total_paid) as total_paid
            ')
            ->first();

        return (float)($totals->total_bills - $totals->total_discounts - $totals->total_paid);
    }

    // public function determinePayS($sponsor)
    // {
    //     return max($sponsor->allPaidPrescriptions(), $sponsor->allPayments());
    // }

    public function getPatientPaymentTable(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->payment->select('id', 'pay_method_id', 'user_id', 'amount_paid', 'comment', 'created_at')
                            ->with(['payMethod:id,name', 'user:id,username'])
                            ->where('visit_id', $data->visitId);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            $query->whereRelation('payMethod', 'name', 'LIKE',  $searchTerm)
                    ->whereRelation('user', 'username', 'LIKE', $searchTerm);
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
                'discount'          => $request->discount,
                'discount_by'       => $user->id
            ]);

            $this->totalsService->syncVisitTotals($visit);

            // $visit->update([
            //     'total_hms_bill'    => $visit->totalHmsBills(),
            //     'total_nhis_bill'   => $visit->sponsor->category_name == 'NHIS' ? $visit->totalNhisBills() : 0,
            //     'discount'          => $request->discount,
            //     'discount_by'       => $user->id
            // ]);

            // $patientId = $visit->patient_id;
            
            // DB::table('patients')->where('id', $patientId)->update([
            //     'total_discount'  => DB::raw("(SELECT COALESCE(SUM(discount), 0) FROM visits WHERE patient_id = $patientId)"),
            // ]);
            return $visit;
        });

    }

    public function processPaymentDestroy(Payment $payment, bool $softDelete, $data)
    {
        return $this->paymentService->destroyPayment($payment, $softDelete, $data);
    }

    public function getVisitsWithOutstandingBills(DataTableQueryParams $params, $data)
    {
        $orderBy  = 'created_at';
        $orderDir = 'desc';
        $page     = ($params->length + $params->start) / $params->length;

        $query = $this->visit->query()
            ->select([
                'id', 'sponsor_id', 'patient_id', 'doctor_id', 'admission_status', 
                'ward', 'visit_type', 'discharge_reason', 'doctor_done_at', 
                'closed', 'closed_opened_by', 'closed_opened_at', 'discount', 
                'total_hms_bill', 'total_nhis_bill', 'total_paid', 'consulted', 'created_at'
            ])
            ->with([
                'sponsor:id,name,category_name,flag', 
                'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment',
                'patient' => fn($q) => $q->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                                        ->with(['flaggedBy:id,username']), 
                'doctor:id,username', 
                'closedOpenedBy:id,username',
            ]);

        // 1. Primary Filter Logic
        // $query->when($data->sponsorId, fn($q) => $q->where('sponsor_id', $data->sponsorId))
        //     ->when($data->cardNo, fn($q) => $q->whereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($data->cardNo, '%_') . '%'))
        //     ->when(!$data->sponsorId && !$data->cardNo, fn($q) => $q->where('patient_id', (int)$data->patientId));

        // 1. Primary Filter Logic (Sponsor/Patient)
        // We wrap these in a group so they stay "locked"
        $query->where(function($q) use ($data) {
            $q->when($data->sponsorId, fn($sq) => $sq->where('sponsor_id', $data->sponsorId))
            ->when($data->cardNo, fn($sq) => $sq->whereRelation('patient', 'card_no', 'LIKE', addcslashes($data->cardNo, '%_') . '%'))
            ->when(!$data->sponsorId && !$data->cardNo, fn($sq) => $sq->where('patient_id', (int)$data->patientId));
        });

        // 2. Search Nuance (Now combined correctly with the Bill check)
        if (!empty($params->searchTerm)) {
            $query->whereHas('patient', function ($q) use ($params) {
                $q->searchByName($params->searchTerm)
                ->orWhereRelation('patient', 'card_no', 'LIKE', addcslashes($params->searchTerm, '%_') . '%');
            });
        }

        // 3. The "Smart Integrity" Check
        $query->where(function ($q) {
            // If the visit's sponsor is NHIS, check the NHIS bill column
            $q->whereHas('sponsor', function ($sq) {
                $sq->where('category_name', 'NHIS');
            })->whereColumn('total_nhis_bill', '!=', 'total_paid')
            
            // OR: If the visit's sponsor is NOT NHIS, check the HMS bill column
            ->orWhere(function ($sq) {
                $sq->whereHas('sponsor', function ($ssq) {
                    $ssq->where('category_name', '!=', 'NHIS');
                })->whereColumn('total_hms_bill', '!=', 'total_paid');
            });
        });

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, ['*'], 'page', $page);
    }

    public function getPatientBillSummaryTable($data)
    {
        return DB::table('prescriptions')
                    ->selectRaw('
                    SUM(prescriptions.hms_bill) as totalBill, 
                    SUM(prescriptions.nhis_bill) as totalNhisBill, 
                    SUM(prescriptions.paid) as totalPaid, 
                    resources.'.$data->type.' as service, 
                    COUNT(resources.category) as types, 
                    SUM(prescriptions.qty_billed) as quantity, 
                    visits.discount as discount, 
                    sponsors.category_name as sponsorCat')
                    ->join('resources', 'prescriptions.resource_id', '=', 'resources.id')
                    ->join('visits', 'prescriptions.visit_id', '=', 'visits.id')
                    ->join('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
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
