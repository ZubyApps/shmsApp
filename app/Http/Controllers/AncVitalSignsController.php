<?php

namespace App\Http\Controllers;

use App\Models\AncVitalSigns;
use App\Http\Requests\StoreAncVitalSignsRequest;
use App\Services\AncVitalSignsService;
use App\Services\DatatablesService;
use Illuminate\Http\Request;

class AncVitalSignsController extends Controller
{
    public function  __construct(
        private readonly DatatablesService $datatablesService,
        private readonly AncVitalSignsService $ancVitalSignsService)
    {
        
    }

    public function store(StoreAncVitalSignsRequest $request)
    {
        $vitalSigns = $this->ancVitalSignsService->create($request, $request->user());
        
        return $vitalSigns;
    }

    public function loadAncVitalSignsTableByVisit(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $vitalSigns = $this->ancVitalSignsService->getPaginatedAncVitalSignsByVisit($params, $request);
       
        $loadTransformer = $this->ancVitalSignsService->getAncVitalSignsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $vitalSigns, $params);  
    }

    public function loadAncVitalSignsChartByVisit(Request $request)
    {
        $vitalSigns = $this->ancVitalSignsService->getAncVitalSignsChartData($request);
        $loadTransformer = $this->ancVitalSignsService->getAncVitalSignsTransformer();

        $outGoing = array_map($loadTransformer, (array)$vitalSigns->getIterator());

        return response()->json($outGoing);
    }

    public function destroy(AncVitalSigns $ancVitalSigns)
    {
        return $ancVitalSigns->destroy($ancVitalSigns->id);
    }
}
