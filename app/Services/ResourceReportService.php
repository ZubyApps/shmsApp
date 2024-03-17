<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Prescription;
use App\Models\Resource;
use App\Models\ResourceCategory;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class ResourceReportService
{
    public function __construct(
        private readonly Resource $resource, 
        private readonly ResourceCategory $resourceCategory, 
        private readonly HelperService $helperService,
        private readonly Prescription $prescription,
        )
    {
    }

    public function getResourceValueSummary(DataTableQueryParams $params, $data)
    {
        return DB::table('resources')
        ->selectRaw('COUNT(DISTINCT(resource_sub_categories.id)) as subCategoryCount, resource_categories.name AS rCategory, COUNT(resources.id) as resourceCount, SUM(purchase_price * stock_level) as purchacedValue, SUM(selling_price * stock_level) as sellValue, SUM(stock_level) as stockLevel')
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
            ->selectRaw('resource_categories.name AS rCategory, COUNT(DISTINCT(resources.id)) as resourceCount, COUNT(prescriptions.id) as prescriptionsCount, SUM(prescriptions.qty_billed * purchase_price) as expectedCost, SUM(prescriptions.qty_dispensed * purchase_price) as dispensedCost, SUM(prescriptions.hms_bill) as expectedIncome, SUM(prescriptions.paid) as actualIncome, SUM(prescriptions.qty_dispensed * selling_price) as dispensedIncome')
                ->leftJoin('resource_sub_categories', 'resource_categories.id', '=', 'resource_sub_categories.resource_category_id')
                ->leftJoin('resources', 'resource_sub_categories.id', '=', 'resources.resource_sub_category_id')
                ->leftJoin('prescriptions', 'resources.id', '=', 'prescriptions.resource_id')
                // ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                ->whereBetween('prescriptions.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->groupBy('rCategory')
                ->orderBy('rCategory', 'desc')
                ->get()
                ->toArray();
        }


        return DB::table('resource_categories')
        ->selectRaw('resource_categories.name AS rCategory, COUNT(DISTINCT(resources.id)) as resourceCount, COUNT(prescriptions.id) as prescriptionsCount, SUM(prescriptions.qty_billed * purchase_price) as expectedCost, SUM(prescriptions.qty_dispensed * purchase_price) as dispensedCost, SUM(prescriptions.hms_bill) as expectedIncome, SUM(prescriptions.paid + prescriptions.capitation) as actualIncome, SUM(prescriptions.qty_dispensed * selling_price) as dispensedIncome')
            ->leftJoin('resource_sub_categories', 'resource_categories.id', '=', 'resource_sub_categories.resource_category_id')
            ->leftJoin('resources', 'resource_sub_categories.id', '=', 'resources.resource_sub_category_id')
            ->leftJoin('prescriptions', 'resources.id', '=', 'prescriptions.resource_id')
            // ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
            ->whereMonth('prescriptions.created_at', $current->month)
            ->whereYear('prescriptions.created_at', $current->year)
            ->groupBy('rCategory')
            ->orderBy('rCategory', 'desc')
            ->get()
            ->toArray();
    }

    public function getUsedResourcesTransformer(): callable
    {
       return  function (ResourceCategory $resourceCategory) {
            return [
                'id'                => $resourceCategory->id,
                'category'          => $resourceCategory->name,
                'subCategoryCount'  => $resourceCategory->resourceSubCategories->count(),
                'prescriptions'     => $resourceCategory->resourceSubCategories->resources->prescriptions->count(),
                'hmsBill'           => $resourceCategory->resourceSubCategories->resources->resources->prescriptions->sum('hms_bill'),
                'paid'              => $resourceCategory->resourceSubCategories->resources->resourceSubCategories->prescriptions->sum('paid'),
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
                    'diagnosis'         => $pConsultation->icd11_diagnosis ?? $pConsultation->provisional_diagnosis,
                    'doctor'            => $pConsultation->user->username,
                    'Hmsbill'           => $prescription->hms_bill,
                    'Hmobill'           => $prescription->hmo_bill,
                    'paid'              => $prescription->paid,
                ];
            };
    }
}
