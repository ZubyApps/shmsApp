<?php

namespace App\Http\Controllers;

use App\Models\MedicationChart;
use App\Http\Requests\StoreMedicationChartRequest;
use App\Http\Requests\UpdateMedicationChartRequest;
use App\Services\DatatablesService;
use App\Services\MedicationChartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MedicationChartController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly MedicationChartService $medicationChartService
    )
    {   
    }

    public function store(StoreMedicationChartRequest $request)
    {
        $chart = $this->medicationChartService->create($request, $request->user());

        return $chart;
    }

    public function loadMedicationChartByPrescription(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $medicationCharts = $this->medicationChartService->getPaginatedMedicationCharts($params, $request);
       
        $loadTransformer = $this->medicationChartService->getLoadByPrescriptionsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $medicationCharts, $params);  
    }

    public function loadUpcomingMedications(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $upcomingMedications = $this->medicationChartService->getUpcomingMedications($params, $request);
       
        $transformer = $this->medicationChartService->upcomingMedicationsTransformer();

        return $this->datatablesService->datatableResponse($transformer, $upcomingMedications, $params);  
    }

    public function saveGivenData(UpdateMedicationChartRequest $request, MedicationChart $medicationChart)
    {
        return $this->medicationChartService->updateRecord($request, $medicationChart, $request->user());
    }

    public function removeGivenData(MedicationChart $medicationChart)
    {
        return $this->medicationChartService->removeRecord($medicationChart);
    }

    public function destroy(MedicationChart $medicationChart)
    {
        return $medicationChart->destroy($medicationChart->id);
    }
}
