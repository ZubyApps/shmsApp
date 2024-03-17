<?php

namespace App\Http\Controllers;

use App\Services\AccountsReportService;
use App\Services\CapitationPaymentService;
use App\Services\DatatablesService;
use App\Services\HospitalAndOthersReportService;
use App\Services\InvestigationReportService;
use App\Services\MedReportService;
use App\Services\PatientReportService;
use App\Services\PharmacyReportService;
use App\Services\ResourceReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private readonly SponsorCategoryController $sponsorCategoryController, 
        private readonly DatatablesService $datatablesService, 
        private readonly PatientReportService $PatientReportService,
        private readonly MedReportService $medReportService,
        private readonly InvestigationReportService $investigationReportService,
        private readonly PharmacyReportService $pharmacyReportService,
        private readonly HospitalAndOthersReportService $hospitalAndOthersReportService,
        private readonly ResourceReportService $resourceReportService,
        private readonly AccountsReportService $accountsReportService,
        private readonly CapitationPaymentService $capitationPaymentService
        )
    {
        
    }

    public function index()
    {
        return view('reports.reports');
    }

    /** Patients reports */    
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

    /** Medical Services Report */
    public function indexMedServices()
    {
        return view('reports.medServices');
    }

    public function loadMedServicesSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->medReportService->getMedServicesSummary($params, $request);

        $loadTransformer = $this->medReportService->getMedServicesTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadByResource(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->medReportService->getPatientsByResource($params, $request);

        $loadTransformer = $this->medReportService->getByResourceTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    /** Medical Services Report */
    public function indexInvestigations()
    {
        return view('reports.investigations');
    }

    public function loadInvestigationsSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->investigationReportService->getInvestigationsSummary($params, $request);

        $loadTransformer = $this->investigationReportService->getInvestigationsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    /** Medical Services Report */
    public function indexPharmacy()
    {
        return view('reports.pharmacy');
    }

    public function loadPharmacySummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->pharmacyReportService->getPharmacySummary($params, $request);

        $loadTransformer = $this->pharmacyReportService->getPharmacyTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    /** Medical Services Report */
    public function indexHospitalAndOthers()
    {
        return view('reports.hospitalAndOthers');
    }

    public function loadHospitalAndOthersSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->hospitalAndOthersReportService->getHospitalAndOthersSummary($params, $request);

        $loadTransformer = $this->hospitalAndOthersReportService->getHospitalAndOthersTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function indexResources()
    {
        return view('reports.resources');
    }
    
    public function loadResourceValueSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $resources = $this->resourceReportService->getResourceValueSummary($params, $request);
        
        return response()->json([
            'data' => $resources,
            'draw' => $params->draw,
            'recordsTotal' => count($resources),
            'recordsFiltered' => count($resources)
        ]);
    }
    
    public function loadUsedResourcesSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
        
        $categories = $this->resourceReportService->getUsedResourcesSummary($params, $request);
        
        // $loadTransformer = $this->resourceReportService->getUsedResourcesTransformer();
        
        return response()->json([
            'data' => $categories,
            'draw' => $params->draw,
            'recordsTotal' => count($categories),
            'recordsFiltered' => count($categories)
        ]);
        
        // return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function indexAccounts()
    {
        return view('reports.accounts');
    }

    public function loadPayMethodsSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $payMethods = $this->accountsReportService->getPaymethodsSummary($params, $request);
        
        return response()->json([
            'data' => $payMethods,
            'draw' => $params->draw,
            'recordsTotal' => count($payMethods),
            'recordsFiltered' => count($payMethods)
        ]);
    }

    public function loadCapitationPayments(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->capitationPaymentService->getCapitationPayments($params, $request);

        $loadTransformer = $this->capitationPaymentService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }
}
