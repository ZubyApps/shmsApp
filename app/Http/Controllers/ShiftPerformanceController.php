<?php

namespace App\Http\Controllers;

use App\Models\ShiftPerformance;
use App\Http\Requests\UpdateShiftPerformanceRequest;
use App\Services\DatatablesService;
use App\Services\ShiftPerformanceService;
use Illuminate\Http\Request;

class ShiftPerformanceController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly ShiftPerformanceService $shiftPerformanceService
        )
    {
    }

    public function UpdateDeptPreformance(Request $request)
    {
        return $this->shiftPerformanceService->update();
    }

    /**
     * Display the specified resource.
     */
    public function loadShiftPerformanceTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $nursesShiftPerformance = $this->shiftPerformanceService->getShiftPerformance($params, $request);
       
        $loadTransformer = $this->shiftPerformanceService->getNursesShiftPerformanceTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $nursesShiftPerformance, $params);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ShiftPerformance $shiftPerformance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function UpdateStaffNames(UpdateShiftPerformanceRequest $request, ShiftPerformance $shiftPerformance)
    {
        return $this->shiftPerformanceService->updateStaff($request, $shiftPerformance);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShiftPerformance $shiftPerformance)
    {
        //
    }
}
