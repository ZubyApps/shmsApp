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

    /**
     * Store a newly created resource in storage.
     */
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

    public function saveDoneData(UpdateNursingChartRequest $request, NursingChart $nursingChart)
    {
        return $this->nursingChartService->updateRecord($request, $nursingChart, $request->user());
    }

    public function removeDoneData(NursingChart $nursingChart)
    {
        return $this->nursingChartService->removeRecord($nursingChart);
    }

    /**
     * Display the specified resource.
     */
    public function show(NursingChart $nursingChart)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NursingChart $nursingChart)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNursingChartRequest $request, NursingChart $nursingChart)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NursingChart $nursingChart)
    {
        return $nursingChart->destroy($nursingChart->id);
    }
}
