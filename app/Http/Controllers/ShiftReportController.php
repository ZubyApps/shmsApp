<?php

namespace App\Http\Controllers;

use App\Models\ShiftReport;
use App\Http\Requests\StoreShiftReportRequest;
use App\Http\Requests\UpdateShiftReportRequest;
use App\Http\Resources\NursesReportResource;
use App\Http\Resources\ShiftReportResource;
use App\Services\DatatablesService;
use App\Services\ShiftReportService;
use Illuminate\Http\Request;

class ShiftReportController extends Controller
{

    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly ShiftReportService $shiftReportService
        )
    {
    }

    public function store(StoreShiftReportRequest $request)
    {
        return $this->shiftReportService->create($request, $request->user());
    }

    public function loadShiftReportTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $nursesReport = $this->shiftReportService->getShiftReports($params, $request);
       
        $loadTransformer = $this->shiftReportService->getShiftReportTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $nursesReport, $params);  
    }

    public function edit(ShiftReport $shiftReport)
    {
        return new ShiftReportResource($shiftReport);
    }

    public function view(Request $request, ShiftReport $shiftReport)
    {
        $this->shiftReportService->mark($shiftReport, $request->user());

        return new ShiftReportResource($shiftReport);
    }

    public function update(UpdateShiftReportRequest $request, ShiftReport $shiftReport)
    {
        return $this->shiftReportService->update($request, $shiftReport, $request->user());
    }

    public function destroy(ShiftReport $shiftReport)
    {
        return $shiftReport->destroy($shiftReport->id);
    }
}
