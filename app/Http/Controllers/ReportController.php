<?php

namespace App\Http\Controllers;

use App\Services\DatatablesService;
use App\Services\PatientReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private readonly SponsorCategoryController $sponsorCategoryController, 
        private readonly DatatablesService $datatablesService, 
        private readonly PatientReportService $PatientReportService,
        // private readonly VisitService $visitService
        )
    {
        
    }

    public function index()
    {
        return view('reports.reports');
    }

    public function indexPatients()
    {
        return view('reports.patients');
    }

    public function loadPatientsDistribution1(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->PatientReportService->getPatientsDistribution1($params, $request);

        return response()->json([
            'data' => $sponsors,
            'draw' => $params->draw,
            'recordsTotal' => count($sponsors),
            'recordsFiltered' => count($sponsors)
        ]);
    }

    public function loadPatientsDistribution2(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->PatientReportService->getPatientsDistribution2($params, $request);

        return response()->json([
            'data' => $sponsors,
            'draw' => $params->draw,
            'recordsTotal' => count($sponsors),
            'recordsFiltered' => count($sponsors)
        ]);
    }

    public function loadPatientFrequency(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $patients = $this->PatientReportService->getPatientFrequency($params, $request);
        
        $loadTransformer = $this->PatientReportService->getFrequencyTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadBySponsor(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->PatientReportService->getBySponsor($params, $request);

        $loadTransformer = $this->PatientReportService->getBySponsorTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadBySponsorMonth(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->PatientReportService->getBySponsorMonth($params, $request);

        $loadTransformer = $this->PatientReportService->getBySponsorTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadRegSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $summary = $this->PatientReportService->getRegSummary($params, $request);

        $loadTransformer = $this->PatientReportService->getRegTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $summary, $params);
    }
}
