<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
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
        private readonly Prescription $prescription,
        private readonly Consultation $consultation,
        private readonly PaymentService $paymentService,
        private readonly PayPercentageService $payPercentageService,
        private readonly HelperService $helperService
        )
    {
        
    }

    public function getpaginatedFilteredPharmacyVisits(DataTableQueryParams $params, $data)
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
                        ->orWhereRelation('sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Outpatient'){
            return $this->visit
            ->where('consulted', '!=', null)
            ->where('pharmacy_done_by', null)
            ->where(function (Builder $query) {
                $query->whereHas('prescriptions', function(Builder $query){
                    $query->where(function (Builder $query) {
                        $query->whereRelation('resource', 'category', '=', 'Medications')
                        ->orWhereRelation('resource', 'category', '=', 'Consumables');
    
                    });
                });
            })
            ->where('admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('pharmacy_done_by', null)
                    ->where('admission_status', '!=', 'Outpatient')
                    ->whereHas('prescriptions', function(Builder $query){
                        $query->where(function (Builder $query) {
                            $query->whereRelation('resource', 'category', '=', 'Medications')
                            ->orWhereRelation('resource', 'category', '=', 'Consumables');  
                        });
                    })
                    ->where('admission_status', '!=', 'Outpatient')
                    ->whereRelation('patient', 'patient_type', '!=', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('pharmacy_done_by', null)
                    ->whereHas('prescriptions', function(Builder $query){
                        $query->where(function (Builder $query) {
                            $query->whereRelation('resource', 'category', '=', 'Medications')
                            ->orWhereRelation('resource', 'category', '=', 'Consumables');     
                        });
                    })
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('pharmacy_done_by', null)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPharmacyVisitsTransformer(): callable
    {
       return  function (Visit $visit) {
            return [
                'id'                => $visit->id,
                'came'              => (new Carbon($visit->consulted))->format('d/m/y g:ia'),
                'patient'           => $visit->patient->patientId(),
                'doctor'            => $visit->doctor->username,
                'ancRegId'          => $visit->antenatalRegisteration?->id,
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => $visit->admission_status,
                'ward'              => $visit->ward ?? '',
                'bedNo'             => $visit->bed_no ?? '',
                'patientType'       => $visit->patient->patient_type,
                'countPrescribed'   => Prescription::where('visit_id', $visit->id)
                                        ->where(function (Builder $query) {
                                            $query->whereRelation('resource', 'category', '=', 'Medications')
                                            ->orWhereRelation('resource', 'category', '=', 'Consumables');
                                        })
                                        ->count(),
                'countBilled'       => Prescription::where('visit_id', $visit->id)
                                        ->where(function (Builder $query) {
                                            $query->whereRelation('resource', 'category', '=', 'Medications')
                                            ->orWhereRelation('resource', 'category', '=', 'Consumables');
                                        })
                                        ->where('qty_billed', '!=', 0)
                                        ->count(),
                'countDispensed'    => Prescription::where('visit_id', $visit->id)
                                        ->where(function (Builder $query) {
                                            $query->whereRelation('resource', 'category', '=', 'Medications')
                                            ->orWhereRelation('resource', 'category', '=', 'Consumables');
                                        })
                                        ->where('qty_dispensed', '!=', 0)
                                        ->count(),
                'sponsorCategory'   => $visit->sponsor->sponsorCategory->name,
                'payPercent'        => $this->payPercentageService->individual_Family($visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($visit),
                'discharged'        => $visit->discharge_reason,
                'doctorDoneAt'      => $visit->doctor_done_at ? (new Carbon($visit->doctor_done_at))->format('d/m/y g:ia') : '',
                'reason'            => $visit->discharge_reason,
                'closed'            => $visit->closed,
            ];
         };
    }

    public function bill(Request $data, Prescription $prescription, User $user)
    {
        return DB::transaction(function () use($data, $prescription, $user) {
            $bill = 0;
            $nhisBill = fn($value)=>$value/10;
            if ($data->quantity){
                $bill = $prescription->resource->selling_price * $data->quantity;
            }
            $prescription->update([
                'qty_billed'        => $data->quantity ?? 0,
                'hms_bill'          => $bill,
                'hms_bill_date'     => $bill ? new Carbon() : null,
                'hms_bill_by'       => $bill ? $user->id : null,
            ]);

            $isNhis = $prescription->visit->sponsor->category_name == 'NHIS';

            $isNhis && $bill ? $prescription->update(['nhis_bill' => $nhisBill($bill)]) : '';

            $prescription->visit->update([
                'total_hms_bill'    => $prescription->visit->totalHmsBills(),
                'total_nhis_bill'   => $isNhis ? $prescription->visit->totalNhisBills() : 0,
                'total_capitation'  => $isNhis ? $prescription->visit->totalPrescriptionCapitations() : 0
            ]);
 
            if ($isNhis){
                $this->paymentService->prescriptionsPaymentSeiveNhis($prescription->visit->totalPayments(), $prescription->visit->prescriptions);
            } else {
                $this->paymentService->prescriptionsPaymentSeive($prescription->visit->totalPayments(), $prescription->visit->prescriptions);
            }
        });
    }

    public function dispense(Request $data, Prescription $prescription, User $user)
    {
        return DB::transaction(function () use($data, $prescription, $user) {
            $resource = $prescription->resource;
            $qtyDispensed = $prescription->qty_dispensed;

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

            // if ($prescription->qty_billed == $prescription->qty_dispensed){
            //     return $prescription->visit->update([
            //         'pharmacy_done_by' => $user->id
            //     ]);
            // }  else {
            //     return $prescription->visit->update([
            //         'pharmacy_done_by' => null
            //     ]);
            // }
            
            if ($prescription->visit->prescriptions->sum('qty_billed') == $prescription->visit->prescriptions->sum('qty_dispensed')){
                return $prescription->visit->update([
                    'pharmacy_done_by' => $user->id
                ]);
            }  else {
                return $prescription->visit->update([
                    'pharmacy_done_by' => null
                ]);
            }

            return $prescription;
        });
    }

    public function hold(Request $data, Prescription $prescription, User $user)
    {
        return $prescription->update([
            'held' => $data->reason,
            'held_at' => new Carbon(),
            'held_by' => $user->id,
        ]);
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

            if (! empty($params->searchTerm)) {
                return $this->consultation
                            ->where('visit_id', $data->visitId)
                            ->where(function (Builder $query) use($params) {
                                $query->where('icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
                                ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescriptions.hmoBillBy', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescriptions.resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

        return $this->consultation
                ->where('visit_id', $data->visitId)
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
                'sponsorCategory'       => $consultation->visit->sponsor->sponsorCategory->name,
                'sponsorCategoryClass'  => $consultation->visit->sponsor->sponsorCategory->pay_class,
                'closed'                => $consultation->visit->closed,
                'prescriptions'         => (new Prescription)->forPharmacy($consultation->id)->map(fn(Prescription $prescription)=> [
                    'id'                => $prescription->id ?? '',
                    'price'             => $prescription->resource?->selling_price ?? '',
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
                    'dispensedBy'       => $prescription->dispensedBy->username ?? '',
                    'dispensed'         => $prescription->dispense_date ? (new Carbon($prescription->dispense_date))->format('d/m/y g:ia') : '',
                    'reason'            => $prescription->held ?? '',
                    'dispenseComment'   => $prescription->dispense_comment ?? '',
                    'note'              => $prescription->note ?? '',
                    'status'            => $prescription->status ?? '',
                    'paid'              => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill,
                    'paidNhis'          => $prescription->paid > 0 && $prescription->paid >= $prescription->hms_bill/10 && $prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
                    'amountPaid'        => $prescription->paid ?? 0,
                ]),
            ];
         };
    }

    public function getExpirationStock(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'expiry_date';
        $orderDir   =  'asc';

        if (! empty($params->searchTerm)) {
            return $this->resource
                        ->where(function (Builder $query) {
                            $query->where('category', 'Medications')
                            ->orWhere('category', 'Consumables')->whereNot('sub_category', 'Lab');
                        })
                        ->where(function (Builder $query) use($params) {
                            $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
                            ->orWhere('category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy === 'expiration'){
            return $this->resource
                    ->where('is_active', true)
                    ->where(function (Builder $query) {
                        $query->where('category', 'Medications')
                        ->orWhere('category', 'Consumables')->whereNot('sub_category', 'Lab');
                    })
                    ->where('expiry_date', '<', (new Carbon())->addMonths(6))
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy === 'stockLevel'){
            return $this->resource
                    ->where('is_active', true)
                    ->where(function (Builder $query) {
                        $query->where('category', 'Medications')
                        ->orWhere('category', 'Consumables')->whereNot('sub_category', 'Lab');
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
                'description'           => $resource->unit_description,
                'sellingPrice'          => $resource->selling_price,
                'expiring'              => $resource->expiry_date ? $this->helperService->twoPartDiffInTimeToCome($resource->expiry_date) : '',
                'prescriptionFrequency' => $resource->prescriptions->where('created_at', '>', (new Carbon())->subDays(30))->count(),
                'dispenseFrequency'     => $resource->prescriptions->where('dispense_date', '>', (new Carbon())->subDays(30))->count(),
                'flag'                  => $resource->expiry_date ? $this->helperService->flagExpired($resource->expiry_date) : '',
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