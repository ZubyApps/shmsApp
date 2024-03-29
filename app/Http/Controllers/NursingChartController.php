<?php

namespace App\Http\Controllers;

use App\Models\NursingChart;
use App\Http\Requests\StoreNursingChartRequest;
use App\Http\Requests\UpdateNursingChartRequest;
use App\Services\DatatablesService;
use App\Services\NursingChartService;
use Illuminate\Http\Request;

class NursingChartController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly NursingChartService $nursingChartService
    )
    { 
    }

    public function store(StoreNursingChartRequest $request)
    {
        $chart = $this->nursingChartService->create($request, $request->user());

        return $chart;
    }

    public function loadNursingChartByPrescription(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $medicationCharts = $this->nursingChartService->getPaginatedNursingCharts($params, $request);
       
        $loadTransformer = $this->nursingChartService->getLoadByPrescriptionsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $medicationCharts, $params);  
    }

    public function loadUpcomingNursingCharts(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $upcomingMedications = $this->nursingChartService->getUpcomingNursingChart($params, $request);
       
        $transformer = $this->nursingChartService->upcomingNursingChartsTransformer();

        return $this->datatablesService->datatableResponse($transformer, $upcomingMedications, $params);  
    }

    public function saveServiceData(UpdateNursingChartRequest $request, NursingChart $nursingChart)
    {
        return $this->nursingChartService->updateRecord($request, $nursingChart, $request->user());
    }

    public function removeServiceData(NursingChart $nursingChart)
    {
        return $this->nursingChartService->removeRecord($nursingChart);
    }

    public function destroy(NursingChart $nursingChart)
    {
        return $nursingChart->destroy($nursingChart->id);
    }
}
