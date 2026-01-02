<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Prescription;
use App\Models\Resource;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ResourceReportService
{
    public function __construct(
        private readonly Resource $resource, 
        private readonly HelperService $helperService,
        private readonly Prescription $prescription,
        )
    {
    }

    public function getResourceValueSummary(DataTableQueryParams $params, $data)
    {
        return DB::table('resources')
        ->selectRaw('COUNT(DISTINCT(resources.resource_sub_category_id)) as subCategoryCount, resource_categories.name AS rCategory, COUNT(resources.id) as resourceCount, SUM(purchase_price * stock_level) as purchacedValue, SUM(selling_price * stock_level) as sellValue, SUM(stock_level) as stockLevel')
            ->leftJoin('resource_sub_categories', 'resources.resource_sub_category_id', '=', 'resource_sub_categories.id')
            ->leftJoin('resource_categories', 'resource_sub_categories.resource_category_id', '=', 'resource_categories.id')
            ->groupBy('rCategory')
            ->orderBy('resourceCount', 'desc')
            ->get()
            ->toArray();
    }

    public function getUsedResourcesSummary(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current = new CarbonImmutable();


        if ($data->startDate && $data->endDate){
            return DB::table('resource_categories')
            ->selectRaw('resource_categories.name AS rCategory, resource_categories.id AS id, COUNT(DISTINCT(resources.id)) as resourceCount, COUNT(prescriptions.id) as prescriptionsCount, SUM(prescriptions.qty_billed * purchase_price) as expectedCost, SUM(prescriptions.qty_dispensed * purchase_price) as dispensedCost, SUM(prescriptions.hms_bill) as expectedIncome, SUM(prescriptions.paid + prescriptions.capitation) as actualIncome, SUM(prescriptions.qty_dispensed * selling_price) as dispensedIncome')
                ->leftJoin('resource_sub_categories', 'resource_categories.id', '=', 'resource_sub_categories.resource_category_id')
                ->leftJoin('resources', 'resource_sub_categories.id', '=', 'resources.resource_sub_category_id')
                ->leftJoin('prescriptions', 'resources.id', '=', 'prescriptions.resource_id')
                ->whereBetween('prescriptions.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->groupBy('rCategory', 'id')
                ->orderBy('rCategory', 'desc')
                ->get()
                ->toArray();
        }

        if ($data->date){
            $date = new Carbon($data->date);

            return DB::table('resource_categories')
        ->selectRaw('resource_categories.name AS rCategory, resource_categories.id AS id, COUNT(DISTINCT(resources.id)) as resourceCount, COUNT(prescriptions.id) as prescriptionsCount, SUM(prescriptions.qty_billed * purchase_price) as expectedCost, SUM(prescriptions.qty_dispensed * purchase_price) as dispensedCost, SUM(prescriptions.hms_bill) as expectedIncome, SUM(prescriptions.paid + prescriptions.capitation) as actualIncome, SUM(prescriptions.qty_dispensed * selling_price) as dispensedIncome')
            ->leftJoin('resource_sub_categories', 'resource_categories.id', '=', 'resource_sub_categories.resource_category_id')
            ->leftJoin('resources', 'resource_sub_categories.id', '=', 'resources.resource_sub_category_id')
            ->leftJoin('prescriptions', 'resources.id', '=', 'prescriptions.resource_id')
            ->whereMonth('prescriptions.created_at', $date->month)
            ->whereYear('prescriptions.created_at', $date->year)
            ->groupBy('rCategory','id')
            ->orderBy('rCategory', 'desc')
            ->get()
            ->toArray();
        }


        return DB::table('resource_categories')
        ->selectRaw('resource_categories.name AS rCategory, resource_categories.id AS id, COUNT(DISTINCT(resources.id)) as resourceCount, COUNT(prescriptions.id) as prescriptionsCount, SUM(prescriptions.qty_billed * purchase_price) as expectedCost, SUM(prescriptions.qty_dispensed * purchase_price) as dispensedCost, SUM(prescriptions.hms_bill) as expectedIncome, SUM(prescriptions.paid + prescriptions.capitation) as actualIncome, SUM(prescriptions.qty_dispensed * selling_price) as dispensedIncome')
            ->leftJoin('resource_sub_categories', 'resource_categories.id', '=', 'resource_sub_categories.resource_category_id')
            ->leftJoin('resources', 'resource_sub_categories.id', '=', 'resources.resource_sub_category_id')
            ->leftJoin('prescriptions', 'resources.id', '=', 'prescriptions.resource_id')
            ->whereMonth('prescriptions.created_at', $current->month)
            ->whereYear('prescriptions.created_at', $current->year)
            ->groupBy('rCategory', 'id')
            ->orderBy('rCategory', 'desc')
            ->get()
            ->toArray();
    }

    // public function getUsedResourcesTransformer(): callable
    // {
    //    return  function (ResourceCategory $resourceCategory) {
    //         return [
    //             'id'                => $resourceCategory->id,
    //             'category'          => $resourceCategory->name,
    //             'subCategoryCount'  => $resourceCategory->resourceSubCategories->count(),
    //             'prescriptions'     => $resourceCategory->resourceSubCategories->resources->prescriptions->count(),
    //             'hmsBill'           => $resourceCategory->resourceSubCategories->resources->resources->prescriptions->sum('hms_bill'),
    //             'paid'              => $resourceCategory->resourceSubCategories->resources->resourceSubCategories->prescriptions->sum('paid'),
    //         ];
    //      };
    // }

    public function getPrescriptionsByResourceCategory(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current    = CarbonImmutable::now();
        $query      = $this->prescription->select('id', 'consultation_id', 'visit_id', 'walk_in_id', 'resource_id', 'created_at', 'hms_bill', 'hmo_bill', 'paid', 'qty_billed', 'qty_dispensed', 'capitation')
                        ->with(
                            [
                                'visit' => function ($query) {
                                    $query->select('id', 'patient_id', 'sponsor_id')
                                    ->with([
                                        'patient:id,first_name,middle_name,last_name,card_no,sex,date_of_birth',
                                        'sponsor:id,name,category_name'

                                    ]);
                                },
                                'consultation' => function ($query) {
                                    $query->select('id', 'icd11_diagnosis', 'provisional_diagnosis', 'assessment', 'user_id')
                                    ->with(['user:id,username']);
                                },
                                'walkIn:id,first_name,middle_name,last_name,sex,date_of_birth',
                                'resource:id,name,sub_category'
                            ]
                        )
                        ->whereRelation('resource.resourceSubCategory.resourceCategory', 'id', '=', $data->resourceCategoryId);

        function applySearch(Builder $query, string $searchTerm){
            $searchTerm = '%' . addcslashes($searchTerm, '%_') . '%';
            return $query->where(function (Builder $query) use($searchTerm) {
                    $query->whereRelation('visit.patient', 'first_name', 'LIKE', $searchTerm )
                    ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', $searchTerm )
                    ->orWhereRelation('visit.patient', 'last_name', 'LIKE', $searchTerm )
                    ->orWhereRelation('visit.patient', 'card_no', 'LIKE', $searchTerm )
                    ->orWhereRelation('visit.sponsor', 'name', 'LIKE', $searchTerm )
                    ->orWhereRelation('visit.sponsor', 'category_name', 'LIKE', $searchTerm );
                });
        }

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                $query = applySearch($query, $params->searchTerm);
                $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->date){
                $date = new Carbon($data->date);

                $query =  applySearch($query, $params->searchTerm);
                return $query->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            $query = applySearch($query, $params->searchTerm);
            return $query->whereMonth('created_at', $current->month)
                    ->whereYear('created_at', $current->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $query->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->date){
            $date = new Carbon($data->date);
            $query->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $query
                ->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLoadTransformer(): callable
    {
        return  function (Prescription $prescription) {

            $pVisit = $prescription->visit;
            $pConsultation = $prescription->consultation;
            $pWalkIn        = $prescription?->walkIn;
            $dateOfBirth    = $pVisit?->patient->date_of_birth ?? $pWalkIn?->date_of_birth;

            return [
                    'id'                => $prescription->id,
                    'date'              => (new Carbon($prescription->created_at))->format('d/M/y g:ia'),
                    'patient'           => $pVisit->patient->patientId(),
                    'sex'               => $pVisit->patient->sex ?? $pWalkIn?->sex,
                    'age'               => $dateOfBirth ? $this->helperService->twoPartDiffInTimePast($dateOfBirth) : '',
                    'sponsor'           => $pVisit->sponsor->name,
                    'category'          => $pVisit->sponsor->category_name,
                    'diagnosis'         => $pConsultation?->icd11_diagnosis ?? $pConsultation?->provisional_diagnosis ?? $pConsultation?->assessment,
                    'doctor'            => $pConsultation?->user->username,
                    'resource'          => $prescription->resource->name,
                    'resourceSubcategory' => $prescription->resource->sub_category,
                    'hmsBill'              => $prescription->hms_bill,
                    'paid'              => $prescription->paid,
                    'capitation'        => $prescription->capitation,
                ];
            };
    }

    public function getResourcesByExpirationOrStock(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'expiry_date';
        $orderDir   =  'asc';
        $query      = $this->resource->select('id', 'name', 'stock_level', 'reorder_level', 'unit_description', 'purchase_price', 'selling_price', 'expiry_date')
                        ->withCount([
                            'prescriptions as prescriptionFrequency' => function($query){
                            $query->where('created_at', '>', (new Carbon())->subDays(30));
                            },
                            'prescriptions as dispenseFrequency' => function($query){
                            $query->where('dispense_date', '>', (new Carbon())->subDays(30));
                            },
                        ]);
        if (! empty($params->searchTerm)) {
            return $query->where(function (Builder $query) use($params) {
                            $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('sub_category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%')
                            ->orWhere('category', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy === 'expiration'){
            return $query->where('expiry_date', '<', (new Carbon())->addMonths(6))
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->filterBy === 'stockLevel'){
            return $query->whereColumn('stock_level', '<=','reorder_level')
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
                'stockLevel'            => $resource->stock_level,
                'reOrderLevel'          => $resource->reorder_level,
                'description'           => $resource->unit_description,
                'purchasePrice'         => $resource->purchase_price,
                'sellingPrice'          => $resource->selling_price,
                'expiring'              => $resource->expiry_date ? $this->helperService->twoPartDiffInTimeToCome($resource->expiry_date) : '',
                'prescriptionFrequency' => $resource->prescriptions->where('created_at', '>', (new Carbon())->subDays(30))->count(),
                'dispenseFrequency'     => $resource->prescriptions->where('dispense_date', '>', (new Carbon())->subDays(30))->count(),
                'flag'                  => $resource->expiry_date ? $this->helperService->flagExpired($resource->expiry_date) : '',
            ];
        };
    }
}
