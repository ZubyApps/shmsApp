<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Events\PrescriptionBilled;
use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\User;
use App\Models\Visit;
use App\Services\HelperService;
use App\Services\PayPercentageService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PharmacyService
{
    public function __construct(
        private readonly Visit $visit, 
        private readonly Resource $resource,
        private readonly Consultation $consultation,
        private readonly PayPercentageService $payPercentageService,
        private readonly HelperService $helperService,
        )
    {
        
    }

    // public function getpaginatedFilteredPharmacyVisits(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'consulted';
    //     $orderDir   =  'desc';
    //     $query = $this->visit
    //         ->select('id', 'patient_id', 'doctor_id', 'sponsor_id', 'doctor_done_by', 'consulted', 'admission_status', 'visit_type', 'discharge_reason', 'discharge_remark', 'closed', 'closed_opened_by', 'closed_opened_at', 'ward', 'bed_no', 'ward_id', 'discount', 'total_hms_bill', 'total_nhis_bill', 'total_paid', 'doctor_done_at')->with([
    //             'sponsor:id,name,category_name,flag', 
    //             'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment', 
    //             'patient' => function($query){
    //                 $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
    //                 ->with(['flaggedBy:id,username']);
    //             },  
    //             'antenatalRegisteration:id', 
    //             'doctor:id,username', 
    //             'closedOpenedBy:id,username',
    //             'wards:id,visit_id,short_name,bed_number'
    //     ])
    //     ->withCount([
    //         'prescriptions as countPrescribed' => function (Builder $query) {
    //         $query->whereRelation('resource', 'category', '=', 'Medications')
    //                 ->orWhereRelation('resource', 'category', '=', 'Consumables');
    //         },
    //         'prescriptions as countBilled' => function (Builder $query) {
    //             $query->where(function (Builder $query) {
    //                 $query->whereRelation('resource', 'category', '=', 'Medications')
    //                 ->orWhereRelation('resource', 'category', '=', 'Consumables');
    //                     })
    //             ->where('qty_billed', '!=', 0);
    //         },
    //         'prescriptions as countDispensed' => function (Builder $query) {
    //             $query->where(function (Builder $query) {
    //                 $query->whereRelation('resource', 'category', '=', 'Medications')
    //                 ->orWhereRelation('resource', 'category', '=', 'Consumables');
    //                     })
    //             ->where('qty_dispensed', '!=', 0);
    //         },
    //     ]);

    //     // function applySearch(Builder $query, string $searchTerm){
    //     //      $searchTerm = '%' . addcslashes($searchTerm, '%_') . '%';
    //     //     return $query->where(function (Builder $query) use($searchTerm) {
    //     //                 $query->where('created_at', 'LIKE', $searchTerm)
    //     //                 ->orWhereRelation('patient', 'first_name', 'LIKE', $searchTerm)
    //     //                 ->orWhereRelation('patient', 'middle_name', 'LIKE', $searchTerm)
    //     //                 ->orWhereRelation('patient', 'last_name', 'LIKE', $searchTerm)
    //     //                 ->orWhereRelation('patient', 'card_no', 'LIKE', $searchTerm)
    //     //                 ->orWhereRelation('consultations', 'icd11_diagnosis', 'LIKE', $searchTerm)
    //     //                 ->orWhereRelation('consultations', 'admission_status', 'LIKE', $searchTerm)
    //     //                 ->orWhereRelation('sponsor', 'name', 'LIKE', $searchTerm)
    //     //                 ->orWhereRelation('sponsor', 'category_name', 'LIKE', $searchTerm);
    //     //             });
    //     // }

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

    //     $prescriptionsConstraints = function(Builder $query){
    //                                     $query->where(function (Builder $query) {
    //                                         $query->whereRelation('resource', 'category', '=', 'Medications')
    //                                         ->orWhereRelation('resource', 'category', '=', 'Consumables');
                        
    //                                 });
    //     };



    //     // if (! empty($params->searchTerm)) {
    //     //     if ($data->filterBy == 'ANC'){
    //     //         $query = applySearch($query, $params->searchTerm);
    //     //         return $query->where('visit_type', 'ANC')
    //     //             ->orderBy($orderBy, $orderDir)
    //     //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     //     }

    //     //     $query = applySearch($query, $params->searchTerm);
    //     //     return $query->orderBy($orderBy, $orderDir)
    //     //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     // }

    //     if (!empty($params->searchTerm)) {
    //         $searchTermRaw = trim($params->searchTerm);
    //         $isPatientIdSearch = str_starts_with($searchTermRaw, 'pId-');
    //         $patientId = $isPatientIdSearch ? explode('-', $searchTermRaw)[1] : null;
            
    //         // Determine the page number for pagination
    //         $page = ($params->length + $params->start) / $params->length;

    //         // Apply the contextual Gatekeeper first
    //         if ($data->filterBy === 'ANC') {
    //             $query->where('visit_type', 'ANC');
    //         }

    //         $query->where(function (Builder $sub) use ($isPatientIdSearch, $patientId, $query, $searchTermRaw) {
                
    //             if ($isPatientIdSearch) {
    //                 $sub->where('patient_id', $patientId);
    //             } else {
    //                 $query = applySearch($query, $searchTermRaw);
    //             }
    //         });

    //         return $query->orderBy($orderBy, $orderDir)
    //                     ->paginate($params->length, ['*'], 'page', $page);
    //     }

    //     if ($data->filterBy == 'Outpatient'){
    //         return $query->where('pharmacy_done_by', null)
    //                 ->whereNotNull('consulted')
    //                 ->whereHas('prescriptions', $prescriptionsConstraints)
    //                 ->where('admission_status', '=', 'Outpatient')
    //                 ->where('visit_type', '!=', 'ANC')
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->filterBy == 'Inpatient'){
    //         return $query->where('pharmacy_done_by', null)
    //                 ->whereNotNull('consulted')
    //                 ->where('admission_status', '!=', 'Outpatient')
    //                 ->whereHas('prescriptions', $prescriptionsConstraints)
    //                 ->where('visit_type', '!=', 'ANC')
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }
    //     if ($data->filterBy == 'ANC'){
    //         return $query->where('pharmacy_done_by', null)
    //                 ->whereHas('prescriptions', $prescriptionsConstraints)
    //                 ->where('visit_type', 'ANC')
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     return $query->where('pharmacy_done_by', null)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    // }

    public function getpaginatedFilteredPharmacyVisits(DataTableQueryParams $params, $data)
    {
        $orderBy  = 'consulted';
        $orderDir = 'desc';
        $page     = ($params->length + $params->start) / $params->length;

        $query = $this->visit->query()
            ->select([
                'id', 'patient_id', 'doctor_id', 'sponsor_id', 'doctor_done_by', 'consulted', 
                'admission_status', 'visit_type', 'discharge_reason', 'closed', 'total_paid', 
                'total_hms_bill', 'total_nhis_bill', 'discount', 'doctor_done_at'
            ])
            ->with([
                'sponsor:id,name,category_name,flag', 
                'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment', 
                'patient:id,flagged_by,flag,first_name,middle_name,last_name,date_of_birth,card_no',
                'patient.flaggedBy:id,username',
                'doctor:id,username', 
                'wards:id,visit_id,short_name,bed_number'
            ])
            ->withCount([
                'prescriptions as countPrescribed' => fn($q) => $q->pharmacyItems(),
                'prescriptions as countBilled'     => fn($q) => $q->pharmacyItems()->where('qty_billed', '!=', 0),
                'prescriptions as countDispensed'  => fn($q) => $q->pharmacyItems()->where('qty_dispensed', '!=', 0),
            ]);

        // --- 1. SEARCH LOGIC (Overrides Status Filters) ---
        if (!empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $searchTerm    = addcslashes($searchTermRaw, '%_') . '%';

            $query->where(function ($sub) use ($searchTerm, $searchTermRaw) {
                // Patient/Card search
                if (str_starts_with($searchTermRaw, 'pId-')) {
                    $sub->where('patient_id', explode('-', $searchTermRaw)[1]);
                } else {
                    $sub->whereHas('patient', fn($p) => $p->searchByName($searchTermRaw)->orWhere('card_no', 'LIKE', $searchTerm)->orWhere('phone', 'LIKE', $searchTerm))
                        ->orWhereHas('sponsor', fn($s) => $s->where('name', 'LIKE', $searchTerm));
                        // ->orWhereHas('latestConsultation', fn($c) => $c->where('icd11_diagnosis', 'LIKE', $searchTerm));
                }
            });
        } 
        
        // --- 2. STATUS/DEPARTMENT FILTERS ---
        else {
            // Shared Gatekeepers for the Pharmacy Worklist
            $query->whereNull('pharmacy_done_by')
                ->whereHas('prescriptions', fn($q) => $q->pharmacyItems());

            if ($data->filterBy === 'Outpatient') {
                $query->whereNotNull('consulted')
                    ->where('admission_status', 'Outpatient')
                    ->where('visit_type', '!=', 'ANC');
            } 
            elseif ($data->filterBy === 'Inpatient') {
                $query->whereNotNull('consulted')
                    ->inpatientOrObservation()
                    ->where('visit_type', '!=', 'ANC');
            } 
            elseif ($data->filterBy === 'ANC') {
                $query->where('visit_type', 'ANC');
            }
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, ['*'], 'page', $page);
    }

    public function getPharmacyVisitsTransformer(): callable
    {
        return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => $visit->consulted ? (new Carbon($visit->consulted))->format('d/m/y g:ia') : 'Not Seen Dr',
                'patient'           => $visit->patient->patientId(),
                'doctor'            => $visit->doctor?->username,
                'conId'             => $visit->latestConsultation?->id,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'diagnosis'         => $visit->latestConsultation?->icd11_diagnosis ?? $visit->latestConsultation?->provisional_diagnosis ?? $visit->latestConsultation?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $visit->ward ? $this->helperService->displayWard($visit) : '',
                'wardId'            => $visit->ward_id ?? '',
                'wardPresent'       => $visit->wards?->visit_id == $visit->id,
                'visitType'         => $visit->visit_type,
                'countPrescribed'   => $visit->countPrescribed,
                'countBilled'       => $visit->countBilled,
                'countDispensed'    => $visit->countDispensed,
                'sponsorCategory'   => $visit->sponsor->category_name,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'doctorDone'        => $visit->doctorDoneBy->username ?? '',
                'reason'            => $visit->discharge_reason,
                'closed'            => $visit->closed,
                'closedBy'          => $visit->closedOpenedBy?->username,
                'flagSponsor'       => $visit->sponsor->flag,
                'flagPatient'       => $visit->patient->flag,
                'flagReason'        => $visit->patient?->flag_reason,
                'flaggedBy'         => $visit->patient->flaggedBy?->username,
                'flaggedAt'         => $visit->patient->flagged_at ? (new Carbon($visit->patient->flagged_at))->format('d/m/y g:ia') : '',
            ];
         };
    }

    public function bill(Request $data, Prescription $prescription, User $user)
    {
        $resource = $prescription->resource;
        $resourceCat = $resource->category;
        $isPhamacyBillable = in_array($resourceCat, ['Medications', 'Consumables']);

        if($prescription->qty_dispensed && $isPhamacyBillable){
            return;
        }
        
        $visit      = $visit = $prescription->visit()->with('sponsor')->first();
        $sponsor    = $visit?->sponsor;
        $isNhis     = $sponsor->category_name == 'NHIS';

        $nhisBill = fn($value)=>$value/10;
        $bill     = 0;
        
        if ($data->quantity){
            $bill = $prescription->resource->getSellingPriceForSponsor($sponsor) * $data->quantity;
        }

        return DB::transaction(function () use($data, $prescription, $user, $visit, $bill, $isNhis, $nhisBill, $isPhamacyBillable) {
              
            $prescriptionUpdates = [
                'qty_billed'        => $data->quantity ?? 0,
                'hms_bill'          => $bill,
                'hms_bill_date'     => $bill ? new Carbon() : null,
                'hms_bill_by'       => $bill ? $user->id : null,
            ];

           if ($isNhis){
                $isNhisBillable = $isPhamacyBillable;

                $approved = $prescription->approved;
                // Apply bill adjustment immediately before the main update
                $prescriptionUpdates['nhis_bill'] = $isNhisBillable ? ( $approved ? $nhisBill($bill) : $bill) : ($approved ? 0 : $bill);
            }

            // 3. Perform Single Prescription Update (1 Query)
            $prescription->update($prescriptionUpdates);

            PrescriptionBilled::dispatch($visit, $isNhis);
            
            return $prescription;
        });
    }

    // public function dispense(Request $data, Prescription $prescription, User $user)
    // {
    //     return DB::transaction(function () use($data, $prescription, $user) {
    //         $resource       = $prescription->resource;
    //         $qtyDispensed   = $prescription->qty_dispensed;

    //         if ($data->quantity){
    //             if ($qtyDispensed){
    //                 $resource->stock_level = $resource->stock_level + $qtyDispensed;
    //                 $resource->save();
    //             }
                
    //             $resource->stock_level = $resource->stock_level - $data->quantity;
    //             $resource->save();

    //         } elseif (!$data->quantity) {
    //             if ($qtyDispensed){
    //                 $resource->stock_level = $resource->stock_level + $qtyDispensed;
    //                 $resource->save();
    //             }
    //         }

    //         $prescription->update([
    //             'qty_dispensed'     => $data->quantity ?? 0,
    //             'dispense_date'     => new Carbon(),
    //             'dispensed_by'      => $user->id
    //         ]);

    //         $visit          = $prescription->visit;

    //         $vPrescriptions = Prescription::where('visit_id', $visit->id)
    //                             ->where(function (Builder $query) {
    //                                 $query->whereRelation('resource', 'category', '=', 'Medications')
    //                                 ->orWhereRelation('resource', 'category', '=', 'Consumables');
    //                             })
    //                             ->get();

    //         $qtyBilled      = $vPrescriptions->sum('qty_billed');
    //         $qtyDispensed   = $vPrescriptions->sum('qty_dispensed');

    //         if ($qtyBilled == $qtyDispensed){
    //             $visit->update([
    //                 'pharmacy_done_by' => $user->id
    //             ]);
    //         }  else {
    //             $visit->update([
    //                 'pharmacy_done_by' => null
    //             ]);
    //         }

    //         return $prescription;
    //     });
    // }

    public function dispense(Request $data, Prescription $prescription, User $user)
    {
        $qB = $prescription->qty_billed;

        if (!$qB || $qB < $data->quantity){
            return;
        }
        
        return DB::transaction(function () use ($data, $prescription, $user) {
            // -----------------------------------------------------------------
            // 1. Load the resource with a row-level lock → no race conditions
            // -----------------------------------------------------------------
            $resource = $prescription->resource()->lockForUpdate()->first();

            // -----------------------------------------------------------------
            // 2. Normalise the two quantities (old & new)
            // -----------------------------------------------------------------
            $oldQty   = $prescription->qty_dispensed ?? 0;   // previously dispensed
            $newQty   = $data->quantity ?? 0;               // what we want to dispense now

            // -----------------------------------------------------------------
            // 3. Compute the new stock level in ONE step
            // -----------------------------------------------------------------
            $newStock = $resource->stock_level + $oldQty - $newQty;

            // Prevent negative stock (optional but strongly recommended)
            // if ($newStock < 0) {
            //     throw new \Exception("Insufficient stock for {$resource->name}");
            // }

            // -----------------------------------------------------------------
            // 4. Persist the stock change in ONE query
            // -----------------------------------------------------------------
            $resource->update(['stock_level' => $newStock]);

            // -----------------------------------------------------------------
            // 5. Update the prescription itself
            // -----------------------------------------------------------------
            $prescription->update([
                'qty_dispensed' => $newQty,
                'dispense_date' => Carbon::now(),
                'dispensed_by'  => $user->id,
            ]);

            // -----------------------------------------------------------------
            // 6. Re-calculate totals for the whole visit (single query)
            // -----------------------------------------------------------------
            $totals = $prescription->visit->prescriptions()
                ->pharmacyItems()
                ->selectRaw('COALESCE(SUM(qty_billed), 0)    AS total_billed')
                ->selectRaw('COALESCE(SUM(qty_dispensed), 0) AS total_dispensed')
                ->first();

            // -----------------------------------------------------------------
            // 7. Mark the pharmacy step as done / undone
            // -----------------------------------------------------------------
            $visit = $prescription->visit;
            $visit->update([
                'pharmacy_done_by' => $totals->total_billed == $totals->total_dispensed
                    ? $user->id
                    : null,
            ]);

            // -----------------------------------------------------------------
            // 8. Return the freshly-updated prescription
            // -----------------------------------------------------------------
            $prescription->setRelation('resource', (object) [
                'id'          => $resource->id,
                'stock_level' => $resource->stock_level, // fresh value after update
            ]);
            
            return $prescription->only(['qty_dispensed']) + ['resource' => $prescription->resource];
        });
    }

    public function saveDispenseComment(Request $data, Prescription $prescription)
    {
        return $prescription->update([
            'dispense_comment' => $data->comment
        ]);
    }

    // public function getPrescriptionsByConsultation(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'created_at';
    //     $orderDir   =  'desc';

    //     $prescriptions = function ($query) {
    //                     $query->select('id', 'visit_id', 'consultation_id', 'created_at', 'resource_id', 'user_id', 'hms_bill_by', 'dispensed_by', 'approved_by', 'rejected_by', 'prescription', 'hms_bill', 'nhis_bill', 'hms_bill_date', 'approved', 'rejected', 'hmo_note', 'qty_dispensed', 'dispense_date', 'held', 'dispense_comment', 'note', 'qty_billed', 'paid')
    //                     ->where(function(Builder $query) {
    //                         $query->whereRelation('resource', 'category', 'Medications')
    //                             ->orWhereRelation('resource', 'category', 'Consumables');
    //                     })
    //                     ->with([
    //                         'resource:id,name,expiry_date,stock_level,category,unit_description,reorder_level,flag',
    //                         'hmsBillBy:id,username',
    //                         'dispensedBy:id,username',
    //                         'approvedBy:id,username',
    //                         'rejectedBy:id,username',
    //                         'user:id,username',
    //                     ])
    //                     ->orderBy('created_at', 'desc');
    //                 };

    //         $query = $this->consultation->select('id', 'user_id', 'visit_id', 'icd11_diagnosis', 'provisional_diagnosis', 'assessment', 'created_at')
    //         ->with([
    //             'visit' => function ($query){
    //                     $query->select('id', 'sponsor_id', 'closed')->with([
    //                         'sponsor' => function ($query){
    //                         $query->select('id', 'name', 'category_name', 'sponsor_category_id')
    //                         ->with(['sponsorCategory:id,pay_class']);
    //                     },
    //                 ]);
    //             }, 
    //             'user:id,username', 
    //             'prescriptions' => $prescriptions 
    //         ]);

    //     if (! empty($params->searchTerm)) {
    //         return $query->where('visit_id', $data->visitId)
    //                     ->where(function (Builder $query) use($params) {
    //                         $query->where('icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                         ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
    //                         ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                         ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
    //                     })
    //                     ->orderBy($orderBy, $orderDir)
    //                     ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     return $query->where('visit_id', $data->visitId)
    //             ->orderBy($orderBy, $orderDir)
    //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    // }

    public function getPrescriptionsByConsultation(DataTableQueryParams $params, Request $data)
    {
        $orderBy  = 'created_at';
        $orderDir = 'desc';
        $page     = ($params->length + $params->start) / $params->length;
        $sponsorId = $this->visit->where('id', $data->visitId)->value('sponsor_id');

        $query = $this->consultation->query()
            ->select(['id', 'user_id', 'visit_id', 'icd11_diagnosis', 'provisional_diagnosis', 'assessment', 'created_at'])
            ->where('visit_id', $data->visitId)
            ->with([
                'user:id,username',
                'visit:id,sponsor_id,closed',
                'visit.sponsor:id,name,category_name,sponsor_category_id',
                'visit.sponsor.sponsorCategory:id,pay_class',
                // Using a cleaner closure for the nested prescriptions
                'prescriptions' => fn($q) => $q->pharmacyItems() // Using the scope!
                    ->select('id', 'visit_id', 'consultation_id', 'created_at', 'resource_id', 'user_id', 'hms_bill_by', 'dispensed_by', 'approved_by', 'rejected_by', 'prescription', 'hms_bill', 'nhis_bill', 'hms_bill_date', 'approved', 'rejected', 'hmo_note', 'qty_dispensed', 'dispense_date', 'held', 'dispense_comment', 'note', 'qty_billed', 'paid')
                    ->with([
                        'resource'=> function($rq) use ($sponsorId) {
                            $rq->select('id', 'name', 'category', 'unit_description_id', 'marked_for_id', 'selling_price')
                                ->with([
                                        'markedFor:id,name',
                                        'unitDescription:id,short_name',
                                        'sponsors' => fn($sq) => $sq->select('sponsors.id')->where('sponsor_id', $sponsorId)->withPivot('selling_price')
                                ]);
                            },
                        'hmsBillBy:id,username',
                        'dispensedBy:id,username',
                        'approvedBy:id,username',
                        'rejectedBy:id,username',
                        'user:id,username',
                    ])
                    ->orderBy('created_at', 'desc')
            ]);

        // --- SEARCH LOGIC ---
        // if (!empty($params->searchTerm)) {
        //     $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';

        //     $query->where(function (Builder $q) use ($searchTerm) {
        //         $q->where('icd11_diagnosis', 'LIKE', $searchTerm)
        //         ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
        //         ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', $searchTerm)
        //         ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', $searchTerm);
        //     });
        // }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, ['*'], 'page', $page);
    }

    // public function getprescriptionByConsultationTransformer(): callable
    // {
    //    return  function (Consultation $consultation) {
    //         return [
    //             'id'                    => $consultation->id,
    //             'consultBy'             => $consultation->user->username,
    //             'diagnosis'             => $consultation->icd11_diagnosis ?? 
    //                                        $consultation->provisional_diagnosis ?? 
    //                                        $consultation->assessment, 
    //             'consulted'             => (new Carbon($consultation->created_at))->format('D d/m/y g:ia'),                
    //             'conId'                 => $consultation->id,
    //             'sponsor'               => $consultation->visit->sponsor->name,
    //             'sponsorCategory'       => $consultation->visit->sponsor->category_name,
    //             'sponsorCategoryClass'  => $consultation->visit->sponsor->sponsorCategory->pay_class,
    //             'closed'                => $consultation->visit->closed,
    //             'prescriptions'         => $consultation->prescriptions->map(fn(Prescription $prescription)=> [
    //                 'id'                => $prescription->id ?? '',
    //                 'price'             => $prescription->resource?->getSellingPriceForSponsor($consultation->visit->sponsor) ?? '',
    //                 'prescribedBy'      => $prescription->user?->username ?? '',
    //                 'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia') ?? '',
    //                 'item'              => $prescription->resource->nameWithIndicators(),
    //                 'stock'             => $prescription->resource->stock_level,
    //                 'category'          => $prescription->resource->category,
    //                 'prescription'      => $prescription->prescription ?? '',
    //                 'qtyBilled'         => $prescription->qty_billed,
    //                 'unit'              => $prescription->resource->unit_description,
    //                 'hmsBill'           => $prescription->hms_bill ?? '',
    //                 'nhisBill'          => $prescription->nhis_bill ?? '',
    //                 'hmsBillBy'         => $prescription->hmsBillBy->username ?? '',
    //                 'billed'            => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
    //                 'approved'          => $prescription->approved, 
    //                 'rejected'          => $prescription->rejected,
    //                 'hmoNote'           => $prescription->hmo_note ?? '',
    //                 'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
    //                 'qtyDispensed'      => $prescription->qty_dispensed,
    //                 'dispensedBy'       => $prescription->dispensedBy?->username ?? '',
    //                 'dispensed'         => $prescription->dispense_date ? (new Carbon($prescription->dispense_date))->format('d/m/y g:ia') : '',
    //                 'reason'            => $prescription->held ?? '',
    //                 'dispenseComment'   => $prescription->dispense_comment ?? '',
    //                 'note'              => $prescription->note ?? '',
    //                 'status'            => $prescription->status ?? '',
    //                 'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
    //                 'paidNhis'          => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->category_name == 'NHIS',
    //                 'amountPaid'        => $prescription->paid ?? 0,
    //                 'blink'             => $prescription->resource->stock_level <= $prescription->resource->reorder_level,
    //                 'flag'              => $prescription->resource->flag
    //             ]),
    //         ];
    //      };
    // }

    public function getprescriptionByConsultationTransformer(): callable
    {
        return function (Consultation $consultation) {
            // Cache visit and sponsor to avoid re-drilling in the loop
            $visit   = $consultation->visit;
            $sponsor = $visit->sponsor;

            return [
                'id'                   => $consultation->id,
                'consultBy'            => $consultation->user->username,
                'diagnosis'            => $consultation->icd11_diagnosis 
                                        ?? $consultation->provisional_diagnosis 
                                        ?? $consultation->assessment, 
                'consulted'            => (new Carbon($consultation?->created_at))->format('D d/m/y g:ia'),
                'conId'                => $consultation->id,
                'sponsor'              => $sponsor->name,
                'sponsorCategory'      => $sponsor->category_name,
                'sponsorCategoryClass' => $sponsor->sponsorCategory->pay_class,
                'closed'               => $visit->closed,
                'prescriptions'        => $consultation->prescriptions->map(function (Prescription $prescription) use ($sponsor) {
                    $resource = $prescription->resource;
                    
                    return [
                        'id'              => $prescription->id,
                        'price'           => $resource?->getSellingPriceForSponsor($sponsor) ?? 0,
                        'prescribedBy'    => $prescription->user?->username ?? '',
                        'prescribed'      => (new Carbon($prescription->created_at))->format('d/m/y g:ia') ?? '',
                        'item'            => $resource?->nameWithIndicators(),
                        'stock'           => $resource?->stock_level ?? 0,
                        'category'        => $resource?->category ?? '',
                        'prescription'    => $prescription->prescription ?? '',
                        'qtyBilled'       => $prescription->qty_billed,
                        'unit'            => $resource?->unit_description ?? '',
                        'hmsBill'         => $prescription->hms_bill ?? 0,
                        'nhisBill'        => $prescription->nhis_bill ?? 0,
                        'hmsBillBy'       => $prescription->hmsBillBy->username ?? '',
                        'billed'          => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
                        'approved'        => $prescription->approved, 
                        'rejected'        => $prescription->rejected,
                        'hmoNote'         => $prescription->hmo_note ?? '',
                        'statusBy'        => $prescription->approvedBy?->username 
                                            ?? $prescription->rejectedBy?->username 
                                            ?? '',
                        'qtyDispensed'    => $prescription->qty_dispensed,
                        'dispensedBy'     => $prescription->dispensedBy?->username ?? '',
                        'dispensed'       => $prescription->dispense_date ? (new Carbon($prescription->dispense_date))->format('d/m/y g:ia') : '',
                        'reason'          => $prescription->held ?? '',
                        'dispenseComment' => $prescription->dispense_comment ?? '',
                        'note'            => $prescription->note ?? '',
                        'status'          => $prescription->status ?? '',
                        // Cleaned up Payment Logic
                        'paid'            => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                        'paidNhis'        => $sponsor->category_name === 'NHIS' && $prescription->paid > 0 && ($prescription->paid >= ($prescription->hms_bill / 10)),
                        'amountPaid'      => $prescription->paid ?? 0,
                        'blink'           => $resource && ($resource->stock_level <= $resource->reorder_level),
                        'flag'            => str_contains($prescription->resource?->flag, $sponsor->category_name) && (!$prescription->approved && !$prescription->rejected) ? true : false,
                    ];
                }),
            ];
        };
    }

    // public function getExpirationStock(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'expiry_date';
    //     $orderDir   =  'asc';
    //     $query      = $this->resource->select('id', 'unit_description_id', 'name', 'category', 'stock_level', 'reorder_level', 'selling_price', 'location', 'expiry_date')
    //                     ->with([
    //                         'unitDescription:id,short_name',
    //                     ])
    //                     ->withCount([
    //                         'prescriptions as prescriptionFrequency' => function($query){
    //                             $query->where('created_at', '>', (new Carbon())->subDays(30));
    //                         },
    //                         'prescriptions as dispenseFrequency' => function($query){
    //                             $query->where('dispense_date', '>', (new Carbon())->subDays(30));
    //                         }
    //                     ]);

    //     function categoryContraint($query){
    //         return $query->where(function (Builder $query) {
    //                         $query->where('category', 'Medications')
    //                         ->orWhere('category', 'Consumables')->whereNot('sub_category', 'Lab');
    //                     });
    //     }

    //     if (! empty($params->searchTerm)) {
    //         $query = categoryContraint($query);
    //         return $query->where(function (Builder $query) use($params) {
    //                         $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
    //                         ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
    //                         ->orWhere('category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
    //                     })
    //                     ->orderBy($orderBy, $orderDir)
    //                     ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->filterBy === 'expiration'){
    //         $query = categoryContraint($query);
    //         return $query->where('is_active', true)
    //                 ->where('stock_level', '>', 0)
    //                 ->where('expiry_date', '<', (new Carbon())->addMonths(6))
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->filterBy === 'stockLevel'){
    //         return $query->where('is_active', true)
    //                 ->where(function (Builder $query) {
    //                     $query->where('category', 'Medications')
    //                     ->orWhere('category', 'Consumables')
    //                     ->whereNot('sub_category', 'Lab');
    //                 })
    //                 ->whereColumn('stock_level', '<','reorder_level')
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }
    // }

    public function getExpirationStock(DataTableQueryParams $params, $data)
    {
        $orderBy  = 'expiry_date';
        $orderDir = 'asc';

        // 1. Initialize Base Query
        $query = $this->resource->select('id', 'unit_description_id', 'name', 'category', 'stock_level', 'reorder_level', 'selling_price', 'location', 'expiry_date')
            ->with(['unitDescription:id,short_name'])
            ->withCount([
                'prescriptions as prescriptionFrequency' => fn($q) => $q->where('created_at', '>', today()->subDays(30)),
                'prescriptions as dispenseFrequency'     => fn($q) => $q->where('dispense_date', '>', today()->subDays(30))
            ]);

        // 2. Apply Global Category Constraints (Consolidated)
        $query->where(function (Builder $q) {
            $q->whereIn('category', ['Medications', 'Consumables'])
            ->where(fn($sq) => $sq->where('sub_category', '!=', 'Lab')
            ->orWhereNull('sub_category')
            );
        });

        // 3. Apply Search Filter
        if (!empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            $query->where(function (Builder $q) use ($searchTerm) {
                $q->where('name', 'LIKE', $searchTerm)
                ->orWhere('sub_category', 'LIKE', $searchTerm)
                ->orWhere('category', 'LIKE', $searchTerm);
            });

            return $query->orderBy($orderBy, $orderDir)
                    ->paginate(
                        $params->length, 
                        ['*'], 
                        'page', 
                        floor($params->start / $params->length) + 1
                    );
        }

        // 4. Apply Specific Filters
        if ($data->filterBy === 'expiration') {
            $query->where('is_active', true)
                ->where('stock_level', '>', 0)
                ->where('expiry_date', '<', now()->addMonths(6));
        }

        if ($data->filterBy === 'stockLevel') {
            $query->where('is_active', true)
                ->whereColumn('stock_level', '<', 'reorder_level');
        }

        // 5. Final Order and Paginate
        return $query->orderBy($orderBy, $orderDir)
                    ->paginate(
                        $params->length, 
                        ['*'], 
                        'page', 
                        floor($params->start / $params->length) + 1
                    );
    }

    public function getExpirationStockTransformer()
    {
        return function (Resource $resource) {
            return [
                'id'                    => $resource->id,
                'name'                  => $resource->name,
                'category'              => $resource->category,
                'stockLevel'            => $resource->stock_level,
                'reOrderLevel'          => $resource->reorder_level,
                'description'           => $resource->unitDescription?->short_name,
                'sellingPrice'          => $resource->selling_price,
                'location'              => $resource->location,
                'expiring'              => $resource->expiry_date ? $this->helperService->twoPartDiffInTimeToCome($resource->expiry_date) : '',
                'prescriptionFrequency' => $resource->prescriptionFrequency,
                'dispenseFrequency'     => $resource->dispenseFrequency,
                'flag'                  => $resource->expiry_date ? $this->helperService->flagExpired($resource->expiry_date) : '',
                'quantity'              => '',
            ];
        };
    }

    public function done(Visit $visit, User $user)
    {
        return $visit->update([
            'pharmacy_done_by' => $user->id
        ]);
    }
}