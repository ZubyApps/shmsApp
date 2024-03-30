<?php

namespace App\Http\Controllers;

use App\Models\VitalSigns;
use App\Http\Requests\StoreVitalSignsRequest;
use App\Services\DatatablesService;
use App\Services\VitalSignsService;
use Illuminate\Http\Request;

class VitalSignsController extends Controller
{
    public function  __construct(
        private readonly DatatablesService $datatablesService,
        private readonly VitalSignsService $vitalSignsService)
    {
    }

    public function store(StoreVitalSignsRequest $request)
    {
        return $this->vitalSignsService->create($request, $request->user());        
    }

    public function loadVitalSignsTableByVisit(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $vitalSigns = $this->vitalSignsService->getPaginatedVitalSignsByVisit($params, $request);
       
        $loadTransformer = $this->vitalSignsService->getVitalSignsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $vitalSigns, $params);  
    }

    public function loadVitalSignsChartByVisit(Request $request)
    {
        $vitalSigns = $this->vitalSignsService->getVitalSignsChartData($request);
        $loadTransformer = $this->vitalSignsService->getVitalSignsTransformer();

        $outGoing = array_map($loadTransformer, (array)$vitalSigns->getIterator());

        return response()->json($outGoing);
    }

    public function destroy(VitalSigns $vitalSigns)
    {
        return $vitalSigns->destroy($vitalSigns->id);
    }
}
