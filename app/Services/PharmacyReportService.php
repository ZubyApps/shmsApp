<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Prescription;
use App\Models\Resource;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class PharmacyReportService
{
    public function __construct(
        private readonly Resource $resource, 
        private readonly HelperService $helperService,
        private readonly Prescription $prescription,
        )
    {
    }

    public function getPharmacySummary(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current = new CarbonImmutable();

        if (! empty($params->searchTerm)) {
            return $this->resource
                        ->where(function (Builder $query) use($params) {
                            $query->where('category', '=', 'Medications')
                                ->orWhere('category', '=', 'Consumables');
                        })
                        ->where(function (Builder $query) use($params) {
                            $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->with([
                            'prescriptions' => function ($query) use ($data, $current) {
                                if ($data->startDate && $data->endDate){
                                    $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                                    ->orderBy('created_at');
                                } else if($data->date){
                                    $date = new Carbon($data->date);
                                    $query->whereMonth('created_at', $date->month)
                                          ->whereYear('created_at', $date->year)
                                          ->orderBy('created_at');
                                } else {
                                    $query->whereMonth('created_at', $current->month)
                                            ->whereYear('created_at', $current->year)
                                    ->orderBy('created_at');
                                }
                            }
                        ,   'bulkRequests' =>  function ($query) use ($data, $current) {
                                if ($data->startDate && $data->endDate){
                                    $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                                    ->orderBy('created_at');
                                } else if($data->date){
                                    $date = new Carbon($data->date);
                                    $query->whereMonth('created_at', $date->month)
                                          ->whereYear('created_at', $date->year)
                                          ->orderBy('created_at');
                                } else {
                                    $query->whereMonth('created_at', $current->month)
                                            ->whereYear('created_at', $current->year)
                                    ->orderBy('created_at');
                                }
                            }])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $this->resource
                    ->where(function (Builder $query) use($params) {
                        $query->where('category', '=', 'Medications')
                            ->orWhere('category', '=', 'Consumables');
                    })
                    ->with([
                        'prescriptions' => function ($query) use ($data, $current) {
                            if ($data->startDate && $data->endDate){
                                $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                                ->orderBy('created_at');
                            } else if($data->date){
                                $date = new Carbon($data->date);
                                $query->whereMonth('created_at', $date->month)
                                      ->whereYear('created_at', $date->year)
                                      ->orderBy('created_at');
                            } else {
                                $query->whereMonth('created_at', $current->month)
                                        ->whereYear('created_at', $current->year)
                                ->orderBy('created_at');
                            }
                        }
                    ,  'bulkRequests' =>  function ($query) use ($data, $current) {
                        if ($data->startDate && $data->endDate){
                            $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->orderBy('created_at');
                        } else if($data->date){
                            $date = new Carbon($data->date);
                            $query->whereMonth('created_at', $date->month)
                                  ->whereYear('created_at', $date->year)
                                  ->orderBy('created_at');
                        } else {
                            $query->whereMonth('created_at', $current->month)
                                    ->whereYear('created_at', $current->year)
                            ->orderBy('created_at');
                        }
                    }])
                    ->withCount('prescriptions as prescriptionCount')
                    ->orderBy('prescriptionCount', $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));       
    }

    public function getPharmacyTransformer(): callable
    {
       return  function (Resource $resource) {
            return [
                'id'                => $resource->id,
                'name'              => $resource->name,
                'subCategory'       => $resource->sub_category,
                'prescriptions'     => $resource->prescriptions->count(),
                'qtyBilled'         => $resource->prescriptions->sum('qty_billed'),
                'qtyDispensed'      => $resource->prescriptions->sum('qty_dispensed'),
                'bulkDispensed'     => $resource->bulkRequests->sum('qty_dispensed'),
            ];
         };
    }

    public function getPatientsByResource(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current    = CarbonImmutable::now();

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                return $this->prescription
                            ->whereRelation('resource', 'id', '=', $data->resourceId)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->prescription
                            ->whereRelation('resource', 'id', '=', $data->resourceId)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereMonth('created_at', $current->month)
                            ->whereYear('created_at', $current->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $this->prescription
                ->whereRelation('resource', 'id', '=', $data->resourceId)
                ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return $this->prescription
                ->whereRelation('resource', 'id', '=', $data->resourceId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $this->prescription
                ->whereRelation('resource', 'id', '=', $data->resourceId)
                ->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getByResourceTransformer(): callable
    {
        return  function (Prescription $prescription) {

            $pVisit = $prescription->visit;
            $pConsultation = $prescription->consultation;

            return [
                    'id'                => $prescription->id,
                    'date'              => (new Carbon($prescription->created_at))->format('d/M/y g:ia'),
                    'patient'           => $pVisit->patient->patientId(),
                    'sex'               => $prescription->visit->patient->sex,
                    'age'               => $this->helperService->twoPartDiffInTimePast($pVisit->patient->date_of_birth),
                    'sponsor'           => $pVisit->sponsor->name,
                    'category'          => $pVisit->sponsor->category_name,
                    'diagnosis'         => $pConsultation?->icd11_diagnosis ?? $pConsultation?->provisional_diagnosis,
                    'doctor'            => $pConsultation?->user?->username,
                    'Hmsbill'           => $prescription->hms_bill,
                    'Hmobill'           => $prescription->hmo_bill,
                    'paid'              => $prescription->paid,
                    'qtyBilled'         => $prescription->qty_billed,
                    'qtyDispensed'      => $prescription->qty_dispensed,
                ];
            };
    }

    public function getMissingSummary(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current = new CarbonImmutable();

        if (! empty($params->searchTerm)) {
            return $this->resource
                        ->where(function (Builder $query) use($params) {
                            $query->where('category', '=', 'Medications')
                                ->orWhere('category', '=', 'Consumables');
                        })
                        ->where(function (Builder $query) use($params) {
                            $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->with([
                            'addResources' => function ($query) use ($data, $current) {
                                if ($data->startDate && $data->endDate){
                                    $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                                    ->orderBy('created_at');
                                } else if($data->date){
                                    $date = new Carbon($data->date);
                                    $query->whereMonth('created_at', $date->month)
                                          ->whereYear('created_at', $date->year)
                                          ->orderBy('created_at');
                                } else {
                                    $query->whereMonth('created_at', $current->month)
                                            ->whereYear('created_at', $current->year)
                                    ->orderBy('created_at');
                                }
                            }
                        ])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $this->resource
                    ->where(function (Builder $query) use($params) {
                        $query->where('category', '=', 'Medications')
                            ->orWhere('category', '=', 'Consumables');
                    })
                    ->whereRelation('addResources', 'difference', '>', 0)
                    ->with([
                        'addResources' => function ($query) use ($data, $current) {
                            if ($data->startDate && $data->endDate){
                                $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                                ->orderBy('created_at');
                            } else if($data->date){
                                $date = new Carbon($data->date);
                                $query->whereMonth('created_at', $date->month)
                                      ->whereYear('created_at', $date->year)
                                      ->orderBy('created_at');
                            } else {
                                $query->whereMonth('created_at', $current->month)
                                        ->whereYear('created_at', $current->year)
                                ->orderBy('created_at');
                            }
                        }
                    ])
                    ->withCount('addResources as addResourcesCount')
                    ->orderBy('addResourcesCount', $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));       
    }

    public function getMissingTransformer(): callable
    {
       return  function (Resource $resource) {
            return [
                'id'                => $resource->id,
                'name'              => $resource->name,
                'category'          => $resource->category,
                'addedResourceCount'=> $resource->addResources->count(),
                'quantity'          => $resource->addResources->sum('quantity'),
                'finalQuantity'     => $resource->addResources->sum('final_quantity'),
                'diff'              => $resource->addResources->sum('difference'),
                'diffPurchase'      => $resource->addResources->sum('difference') * $resource->purchase_price,
                'diffSelling'       => $resource->addResources->sum('difference') * $resource->selling_price,
            ];
         };
    }
}
