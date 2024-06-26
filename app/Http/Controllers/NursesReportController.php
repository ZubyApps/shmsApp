<?php

namespace App\Http\Controllers;

use App\Models\NursesReport;
use App\Http\Requests\StoreNursesReportRequest;
use App\Http\Requests\UpdateNursesReportRequest;
use App\Http\Resources\NursesReportResource;
use App\Models\Visit;
use App\Services\DatatablesService;
use App\Services\NursesReportService;
use Illuminate\Http\Request;

class NursesReportController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly NursesReportService $nursesReportService
        )
    {
    }

    public function store(StoreNursesReportRequest $request, Visit $visit)
    {
        return $this->nursesReportService->create($request, $visit, $request->user());
    }

    public function loadNursesReportTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $nursesReport = $this->nursesReportService->getNursesReports($params, $request);
       
        $loadTransformer = $this->nursesReportService->getNursesReportTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $nursesReport, $params);  
    }

    public function edit(NursesReport $nursesReport)
    {
        return new NursesReportResource($nursesReport);
    }

    public function update(UpdateNursesReportRequest $request, NursesReport $nursesReport)
    {
        return $this->nursesReportService->update($request, $nursesReport, $request->user());
    }

    public function destroy(NursesReport $nursesReport)
    {
        return $nursesReport->destroy($nursesReport->id);
    }
}
