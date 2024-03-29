<?php

namespace App\Http\Controllers;

use App\Models\MedicalReport;
use App\Http\Requests\StoreMedicalReportRequest;
use App\Http\Requests\UpdateMedicalReportRequest;
use App\Http\Resources\DisplayMedicalReportResource;
use App\Http\Resources\MedicalReportResource;
use App\Services\DatatablesService;
use App\Services\MedicalReportService;
use Illuminate\Http\Request;

class MedicalReportController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly MedicalReportService $medicalReportService)
    {
        
    }

    public function store(StoreMedicalReportRequest $request)
    {
        $visit = $this->medicalReportService->create($request, $request->user());
    }

    public function loadMedicalReportTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $medicalReports = $this->medicalReportService->getMedicalReports($params, $request);
       
        $loadTransformer = $this->medicalReportService->getMedicalReportTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $medicalReports, $params);  
    }

    public function edit(MedicalReport $medicalReport)
    {
        return new MedicalReportResource($medicalReport);
    }

    public function displayReport(MedicalReport $medicalReport)
    {
        return new DisplayMedicalReportResource($medicalReport);
    }

    public function update(UpdateMedicalReportRequest $request, MedicalReport $medicalReport)
    {
        return $this->medicalReportService->update($request, $medicalReport, $request->user());
    }

    public function destroy(MedicalReport $medicalReport)
    {
        return $medicalReport->destroy($medicalReport->id);
    }
}
