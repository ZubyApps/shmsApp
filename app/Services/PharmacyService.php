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
        private readonly Consultation $consultation)
    {
        
    }

    public function getpaginatedFilteredPharmacyVisits(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
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
            ->where('closed', null)
            ->where(function(Builder $query) {
                $query->whereRelation('prescriptions.resource', 'category', '=', 'Medications')
                    ->orWhereRelation('prescriptions.resource', 'category', '=', 'Consumables');
            })
            ->whereRelation('consultations', 'admission_status', '=', 'Outpatient')
            ->whereRelation('patient', 'patient_type', '!=', 'ANC')
            ->orderBy($orderBy, $orderDir)
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy == 'Inpatient'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('pharmacy_done_by', null)
                    ->where('closed', null)
                    ->where(function(Builder $query) {
                        $query->whereRelation('prescriptions.resource', 'category', '=', 'Medications')
                            ->orWhereRelation('prescriptions.resource', 'category', '=', 'Consumables');
                    })
                    ->where(function (Builder $query) {
                        $query->whereRelation('consultations', 'admission_status', '=', 'Inpatient')
                        ->orWhereRelation('consultations', 'admission_status', '=', 'Observation');
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->filterBy == 'ANC'){
            return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('pharmacy_done_by', null)
                    ->where('closed', null)
                    ->where(function(Builder $query) {
                        $query->whereRelation('prescriptions.resource', 'category', '=', 'Medications')
                            ->orWhereRelation('prescriptions.resource', 'category', '=', 'Consumables');
                    })
                    ->whereRelation('patient', 'patient_type', '=', 'ANC')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->visit
                    ->where('consulted', '!=', null)
                    ->where('pharmacy_done_by', null)
                    ->where('closed', null)
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
                'diagnosis'         => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->icd11_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->provisional_diagnosis ?? 
                                       Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->assessment,
                'sponsor'           => $visit->sponsor->name,
                'admissionStatus'   => Consultation::where('visit_id', $visit->id)->orderBy('id', 'desc')->first()?->admission_status,
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
                                        ->where('qty_billed', '!=', null)
                                        ->count(),
                'countDispensed'    => Prescription::where('visit_id', $visit->id)
                                        ->where(function (Builder $query) {
                                            $query->whereRelation('resource', 'category', '=', 'Medications')
                                            ->orWhereRelation('resource', 'category', '=', 'Consumables');
                                        })
                                        ->where('qty_dispensed', '!=', null)
                                        ->count(),
            ];
         };
    }

    public function bill(Request $data, Prescription $prescription, User $user)
    {
        return DB::transaction(function () use($data, $prescription, $user) {
            $bill = null;
            if ($data->quantity){
                $bill = $prescription->resource->selling_price * $data->quantity;
            }
            $prescription->update([
                'qty_billed'        => $data->quantity,
                'hms_bill'          => $bill,
                'hms_bill_date'     => $bill ? new Carbon() : null,
                'hms_bill_by'       => $bill ? $user->id : null,
            ]);

            $prescription->visit->update([
                'total_bill' => $data->quantity ? $prescription->visit->totalBills() : ($prescription->visit->totalBills() - $bill)
            ]);
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

            return $prescription->update([
                'qty_dispensed'     => $data->quantity,
                'dispense_date'     => $data->quantity ? new Carbon() : null,
                'dispensed_by'      => $data->quantity ? $user->id : null,
            ]);
        });
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
                'consulted'             => (new Carbon($consultation->created_at))->format('D/m/y g:ia'),                
                'conId'                 => $consultation->id,
                'sponsor'               => $consultation->visit->sponsor->sponsorCategory->name,
                'prescriptions'         => (new Prescription)->forPharmacy($consultation->id)->map(fn(Prescription $prescription)=> [
                    'id'                => $prescription->id ?? '',
                    'price'             => $prescription->resource?->selling_price ?? '',
                    'prescribedBy'      => $prescription->user?->username ?? '',
                    'prescribed'        => (new Carbon($prescription->created_at))->format('D/m/y g:ia') ?? '',
                    'item'              => $prescription->resource->name,
                    'category'          => $prescription->resource->category,
                    'prescription'      => $prescription->prescription ?? '',
                    'qtyBilled'         => $prescription->qty_billed,
                    'unit'              => $prescription->resource->unit_description,
                    'bill'              => $prescription->hms_bill ?? '',
                    'hmsBillBy'         => $prescription->hmsBillBy->username ?? '',
                    'billed'            => $prescription->hms_bill_date ? (new Carbon($prescription->hms_bill_date))->format('d/m/y g:ia') : '',
                    'approved'          => $prescription->approved, 
                    'rejected'          => $prescription->rejected,
                    'statusNote'        => $prescription->approval_note ?? $prescription->rejection_note ?? '',
                    'statusBy'          => $prescription->approvedBy?->username ?? $prescription->rejectedBy?->username ?? '',
                    'qtyDispensed'      => $prescription->qty_dispensed,
                    'dispensedBy'       => $prescription->dispensedBy->username ?? '',
                    'dispensed'         => $prescription->dispense_date ? (new Carbon($prescription->dispense_date))->format('d/m/y g:ia') : '',
                    'note'              => $prescription->note ?? '',
                    'status'            => $prescription->status ?? '',
                ]),
            ];
         };
    }
}