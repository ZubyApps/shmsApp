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

    public function getpaginatedFilteredPharmacyVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'consulted';
        $orderDir   =  'desc';
        $query = $this->visit
            ->select('id', 'patient_id', 'doctor_id', 'sponsor_id', 'consulted', 'admission_status', 'visit_type', 'discharge_reason', 'discharge_remark', 'closed', 'ward', 'bed_no', 'ward_id')->with([
                'sponsor:id,name,category_name,flag', 
                'latestConsultation:id,consultations.visit_id,icd11_diagnosis,provisional_diagnosis,assessment', 
                'patient' => function($query){
                    $query->select('id', 'flagged_by', 'flag', 'flag_reason', 'flagged_at', 'first_name', 'middle_name', 'last_name', 'date_of_birth', 'card_no')
                    ->with(['flaggedBy:id,username']);
                },  
                'antenatalRegisteration:id', 
                'doctor:id,username', 
                'closedOpenedBy:id,username',
                'wards:id,visit_id,short_name,bed_number'
        ])
        ->withCount([
            'prescriptions as countPrescribed' => function (Builder $query) {
            $query->whereRelation('resource', 'category', '=', 'Medications')
                    ->orWhereRelation('resource', 'category', '=', 'Consumables');
            },
            'prescriptions as countBilled' => function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query->whereRelation('resource', 'category', '=', 'Medications')
                    ->orWhereRelation('resource', 'category', '=', 'Consumables');
                        })
                ->where('qty_billed', '!=', 0);
            },
            'prescriptions as countDispensed' => function (Builder $query) {
                $query->where(function (Builder $query) {
                    $query->whereRelation('resource', 'category', '=', 'Medications')
                    ->orWhereRelation('resource', 'category', '=', 'Consumables');
                        })
                ->where('qty_dispensed', '!=', 0);
            },
        ]);

        function applySearch(Builder $query, string $searchTerm){
             $searchTerm = '%' . addcslashes($searchTerm, '%_') . '%';
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
                    });
        }

        $prescriptionsConstraints = function(Builder $query){
                                        $query->where(function (Builder $query) {
                                            $query->whereRelation('resource', 'category', '=', 'Medications')
                                            ->orWhereRelation('resource', 'category', '=', 'Consumables');
                        
                                    });
        };



        if (! empty($params->searchTerm)) {
            if ($data->filterBy == 'ANC'){
                $query = applySearch($query, $params->searchTerm);
                return $query->where('visit_type', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            $query = applySearch($query, $params->searchTerm);
            return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Outpatient'){
            return $query->where('pharmacy_done_by', null)
                    ->whereNotNull('consulted')
                    ->whereHas('prescriptions', $prescriptionsConstraints)
                    ->where('admission_status', '=', 'Outpatient')
                    ->where('visit_type', '!=', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $query->where('pharmacy_done_by', null)
                    ->whereNotNull('consulted')
                    ->where('admission_status', '!=', 'Outpatient')
                    ->whereHas('prescriptions', $prescriptionsConstraints)
                    ->where('visit_type', '!=', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $query->where('pharmacy_done_by', null)
                    ->whereHas('prescriptions', $prescriptionsConstraints)
                    ->where('visit_type', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('pharmacy_done_by', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
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
        $visit    = $visit = $prescription->visit()->with('sponsor')->first();
        $sponsor = $visit?->sponsor;
        $isNhis = $sponsor->category_name == 'NHIS';

        $nhisBill = fn($value)=>$value/10;
        $bill     = 0;
        if ($data->quantity){
            $bill = $prescription->resource->getSellingPriceForSponsor($sponsor) * $data->quantity;
        }

        return DB::transaction(function () use($data, $prescription, $user, $visit, $bill, $isNhis, $nhisBill) {
              
            $prescriptionUpdates = [
                'qty_billed'        => $data->quantity ?? 0,
                'hms_bill'          => $bill,
                'hms_bill_date'     => $bill ? new Carbon() : null,
                'hms_bill_by'       => $bill ? $user->id : null,
            ];

           if ($isNhis){
                $resourceCat = $prescription->resource->category;

                $isNhisBillable = $resourceCat == 'Medications' || $resourceCat == 'Consumables' ;

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

    public function dispense(Request $data, Prescription $prescription, User $user)
    {
        return DB::transaction(function () use($data, $prescription, $user) {
            $resource       = $prescription->resource;
            $qtyDispensed   = $prescription->qty_dispensed;

            if ($data->quantity){
                if ($qtyDispensed){
                    $resource->stock_level = $resource->stock_level + $qtyDispensed;
                    $resource->save();
                }
                
                $resource->stock_level = $resource->stock_level - $data->quantity;
                $resource->save();

            } elseif (!$data->quantity) {
                if ($qtyDispensed){
                    $resource->stock_level = $resource->stock_level + $qtyDispensed;
                    $resource->save();
                }
            }

            $prescription->update([
                'qty_dispensed'     => $data->quantity ?? 0,
                'dispense_date'     => new Carbon(),
                'dispensed_by'      => $user->id
            ]);

            $visit          = $prescription->visit;

            $vPrescriptions = Prescription::where('visit_id', $visit->id)
                                ->where(function (Builder $query) {
                                    $query->whereRelation('resource', 'category', '=', 'Medications')
                                    ->orWhereRelation('resource', 'category', '=', 'Consumables');
                                })
                                ->get();

            $qtyBilled      = $vPrescriptions->sum('qty_billed');
            $qtyDispensed   = $vPrescriptions->sum('qty_dispensed');

            if ($qtyBilled == $qtyDispensed){
                $visit->update([
                    'pharmacy_done_by' => $user->id
                ]);
            }  else {
                $visit->update([
                    'pharmacy_done_by' => null
                ]);
            }

            return $prescription;
        });
    }

    public function saveDispenseComment(Request $data, Prescription $prescription)
    {
        return $prescription->update([
            'dispense_comment' => $data->comment
        ]);
    }

    public function getPrescriptionsByConsultation(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        $prescriptions = function ($query) {
                        $query->select('id', 'visit_id', 'consultation_id', 'created_at', 'resource_id', 'user_id', 'hms_bill_by', 'dispensed_by', 'approved_by', 'rejected_by', 'prescription', 'hms_bill', 'nhis_bill', 'hms_bill_date', 'approved', 'rejected', 'hmo_note', 'qty_dispensed', 'dispense_date', 'held', 'dispense_comment', 'note', 'qty_billed')
                        ->where(function(Builder $query) {
                            $query->whereRelation('resource', 'category', 'Medications')
                                ->orWhereRelation('resource', 'category', 'Consumables');
                        })
                        ->with([
                            'resource:id,name,expiry_date,stock_level,category,unit_description,reorder_level,flag',
                            'hmsBillBy:id,username',
                            'dispensedBy:id,username',
                            'approvedBy:id,username',
                            'rejectedBy:id,username',
                            'user:id,username',
                        ])
                        ->orderBy('created_at', 'desc');
                    };

            $query = $this->consultation->select('id', 'user_id', 'visit_id', 'icd11_diagnosis', 'provisional_diagnosis', 'assessment', 'created_at')
            ->with([
                'visit' => function ($query){
                        $query->select('id', 'sponsor_id', 'closed')->with([
                            'sponsor' => function ($query){
                            $query->select('id', 'name', 'category_name', 'sponsor_category_id')
                            ->with(['sponsorCategory:id,pay_class']);
                        },
                    ]);
                }, 
                'user:id,username', 
                'prescriptions' => $prescriptions 
            ]);

        if (! empty($params->searchTerm)) {
            return $query->where('visit_id', $data->visitId)
                        ->where(function (Builder $query) use($params) {
                            $query->where('icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
                            ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->where('visit_id', $data->visitId)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getprescriptionByConsultationTransformer(): callable
    {
       return  function (Consultation $consultation) {
            return [
                'id'                    => $consultation->id,
                'consultBy'             => $consultation->user->username,
                'diagnosis'             => $consultation->icd11_diagnosis ?? 
                                           $consultation->provisional_diagnosis ?? 
                                           $consultation->assessment, 
                'consulted'             => (new Carbon($consultation->created_at))->format('D d/m/y g:ia'),                
                'conId'                 => $consultation->id,
                'sponsor'               => $consultation->visit->sponsor->name,
                'sponsorCategory'       => $consultation->visit->sponsor->category_name,
                'sponsorCategoryClass'  => $consultation->visit->sponsor->sponsorCategory->pay_class,
                'closed'                => $consultation->visit->closed,
                'prescriptions'         => $consultation->prescriptions->map(fn(Prescription $prescription)=> [
                    'id'                => $prescription->id ?? '',
                    'price'             => $prescription->resource?->getSellingPriceForSponsor($consultation->visit->sponsor) ?? '',
                    'prescribedBy'      => $prescription->user?->username ?? '',
                    'prescribed'        => (new Carbon($prescription->created_at))->format('d/m/y g:ia') ?? '',
                    'item'              => $prescription->resource->nameWithIndicators(),
                    'stock'             => $prescription->resource->stock_level,
                    'category'          => $prescription->resource->category,
                    'prescription'      => $prescription->prescription ?? '',
                    'qtyBilled'         => $prescription->qty_billed,
                    'unit'              => $prescription->resource->unit_description,
                    'hmsBill'           => $prescription->hms_bill ?? '',
                    'nhisBill'          => $prescription->nhis_bill ?? '',
                    'hmsBillBy'         => $prescription->hmsBillBy->username ?? '',
                    'billed'            => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
                    'approved'          => $prescription->approved, 
                    'rejected'          => $prescription->rejected,
                    'hmoNote'           => $prescription->hmo_note ?? '',
                    'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
                    'qtyDispensed'      => $prescription->qty_dispensed,
                    'dispensedBy'       => $prescription->dispensedBy?->username ?? '',
                    'dispensed'         => $prescription->dispense_date ? (new Carbon($prescription->dispense_date))->format('d/m/y g:ia') : '',
                    'reason'            => $prescription->held ?? '',
                    'dispenseComment'   => $prescription->dispense_comment ?? '',
                    'note'              => $prescription->note ?? '',
                    'status'            => $prescription->status ?? '',
                    'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                    'paidNhis'          => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->category_name == 'NHIS',
                    'amountPaid'        => $prescription->paid ?? 0,
                    'blink'             => $prescription->resource->stock_level <= $prescription->resource->reorder_level,
                    'flag'              => $prescription->resource->flag
                ]),
            ];
         };
    }
    public function getExpirationStock(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'expiry_date';
        $orderDir   =  'asc';
        $query      = $this->resource->select('id', 'unit_description_id', 'name', 'category', 'stock_level', 'reorder_level', 'selling_price', 'location', 'expiry_date')
                        ->with([
                            'unitDescription:id,short_name',
                        ])
                        ->withCount([
                            'prescriptions as prescriptionFrequency' => function($query){
                                $query->where('created_at', '>', (new Carbon())->subDays(30));
                            },
                            'prescriptions as dispenseFrequency' => function($query){
                                $query->where('dispense_date', '>', (new Carbon())->subDays(30));
                            }
                        ]);

        function categoryContraint($query){
            return $query->where(function (Builder $query) {
                            $query->where('category', 'Medications')
                            ->orWhere('category', 'Consumables')->whereNot('sub_category', 'Lab');
                        });
        }

        if (! empty($params->searchTerm)) {
            $query = categoryContraint($query);
            return $query->where(function (Builder $query) use($params) {
                            $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
                            ->orWhere('category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy === 'expiration'){
            $query = categoryContraint($query);
            return $query->where('is_active', true)
                    ->where('stock_level', '>', 0)
                    ->where('expiry_date', '<', (new Carbon())->addMonths(6))
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy === 'stockLevel'){
            return $query->where('is_active', true)
                    ->where(function (Builder $query) {
                        $query->where('category', 'Medications')
                        ->orWhere('category', 'Consumables')
                        ->whereNot('sub_category', 'Lab');
                    })
                    ->whereColumn('stock_level', '<','reorder_level')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
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