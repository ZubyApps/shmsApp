<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Resource;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class PharmacyReportService
{
    public function __construct(
        private readonly Resource $resource, 
        )
    {
    }

    public function getPharmacySummary(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current = new CarbonImmutable();
        $dateConstraints  = function ($query) use ($data, $current) {
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
                            };

        $query      = $this->resource->select('id', 'name', 'sub_category')
                        ->withCount(['prescriptions as prescriptionsCount' => $dateConstraints])
                        ->withSum(['prescriptions as qtyBilled' => $dateConstraints], 'qty_billed')
                        ->withSum(['prescriptions as qtyDispensed' => $dateConstraints], 'qty_dispensed')
                        ->withSum(['bulkRequests as sumBulkDispensed' => $dateConstraints], 'qty_dispensed');


        function applyCategoriesFilter(Builder $query, ){
            return $query->where(function (Builder $query) {
                            $query->where('category', '=', 'Medications')
                                ->orWhere('category', '=', 'Consumables');
                        });
        }

        if (! empty($params->searchTerm)) {
            $query   =  applyCategoriesFilter($query);
            return $query->where(function (Builder $query) use($params) {
                            $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        $query   =  applyCategoriesFilter($query);
        return $query->orderBy('prescriptionsCount', $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));       
    }

    public function getPharmacyTransformer(): callable
    {
       return  function (Resource $resource) {
            return [
                'id'                => $resource->id,
                'name'              => $resource->name,
                'subCategory'       => $resource->sub_category,
                'prescriptions'     => $resource->prescriptionsCount ?? 0,//prescriptions->count(),
                'qtyBilled'         => $resource->qtyBilled ?? 0,//prescriptions->sum('qty_billed'),
                'qtyDispensed'      => $resource->qtyDispensed ?? 0,//prescriptions->sum('qty_dispensed'),
                'bulkDispensed'     => $resource->sumBulkDispensed ?? 0,//bulkRequests->sum('qty_dispensed'),
            ];
         };
    }

    public function getMissingSummary(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current = new CarbonImmutable();
        $dateConstraints  = function ($query) use ($data, $current) {
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
                            };
        $query      = $this->resource->select('id', 'name', 'sub_category', 'purchase_price', 'selling_price')
                        ->withCount(['addResources as addResourcesCount' => $dateConstraints])
                        ->withSum(['addResources as quantity' => $dateConstraints], 'quantity')
                        ->withSum(['addResources as finalQuantity' => $dateConstraints], 'final_quantity')
                        ->withSum(['addResources as difference' => $dateConstraints], 'difference')
                        ->whereRelation('addResources', 'difference', '>', 0)
                        ->where(function (Builder $query) use($params) {
                            $query->where('category', '=', 'Medications')
                                ->orWhere('category', '=', 'Consumables');
                        });


        if (! empty($params->searchTerm)) {
            return $query->where(function (Builder $query) use($params) {
                            $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $query->orderBy('addResourcesCount', $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));       
    }

    public function getMissingTransformer(): callable
    {
       return  function (Resource $resource) {
            return [
                'id'                => $resource->id,
                'name'              => $resource->name,
                'category'          => $resource->category,
                'addedResourceCount'=> $resource->addResourcesCount,
                'quantity'          => $resource->quantity,
                'finalQuantity'     => $resource->finalQuantity,
                'diff'              => $resource->difference,
                'diffPurchase'      => $resource->difference * $resource->purchase_price,
                'diffSelling'       => $resource->difference * $resource->selling_price,
            ];
         };
    }
}
