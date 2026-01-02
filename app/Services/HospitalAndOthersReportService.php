<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Resource;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;

class HospitalAndOthersReportService
{
    public function __construct(private readonly Resource $resource)
    {
    }

    public function getHospitalAndOthersSummary(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current = new CarbonImmutable();
        $dateConstraint = function ($query) use ($data, $current) {
                            if ($data->startDate && $data->endDate){
                                $query->whereBetween(
                                    'created_at', 
                                    [
                                        $data->startDate.' 00:00:00', 
                                        $data->endDate.' 23:59:59'
                                    ]
                                )
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
        $query      =   $this->resource->select('id', 'name', 'sub_category')
                        ->HospitalAndOthersCategories()
                        ->withCount(['prescriptions as prescriptionCount' => $dateConstraint])
                        ->withSum(['prescriptions as qtyBilled' => $dateConstraint], 'qty_billed');

        if (! empty($params->searchTerm)) {
            return $query->where(function (Builder $query) use($params) {
                            $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
      
        return $query->orderBy('prescriptionCount', $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));       
    }

    public function getHospitalAndOthersTransformer(): callable
    {
       return  function (Resource $resource) {
            return [
                'id'                => $resource->id,
                'name'              => $resource->name,
                'subCategory'       => $resource->sub_category,
                'prescriptions'     => $resource->prescriptionCount,
                'qtyPrescribed'     => $resource->qtyBilled ?? 0
            ];
         };
    }
}
