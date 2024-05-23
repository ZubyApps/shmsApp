<?php

namespace App\Http\Controllers;

use App\Models\ShiftPerformance;
use App\Http\Requests\StoreShiftPerformanceRequest;
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

    /**
     * Store a newly created resource in storage.
     */
    public function UpdateDeptPreformance(Request $request)
    {
        return $this->shiftPerformanceService->update($request);
    }

    /**
     * Display the specified resource.
     */
    public function show(ShiftPerformance $shiftPerformance)
    {
        //
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
    public function update(UpdateShiftPerformanceRequest $request, ShiftPerformance $shiftPerformance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ShiftPerformance $shiftPerformance)
    {
        //
    }
}
