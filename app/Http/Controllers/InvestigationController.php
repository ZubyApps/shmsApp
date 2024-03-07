<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveLabResultRequest;
use App\Http\Resources\InvestigationResultResource;
use App\Http\Resources\PrintLabTestsCollection;
use App\Models\Prescription;
use App\Models\Visit;
use App\Services\DatatablesService;
use App\Services\InvestigationService;
use Illuminate\Http\Request;

class InvestigationController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly InvestigationService $investigationService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('investigations.investigations');
    }

    public function loadVisitsByFilterLab(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->investigationService->getpaginatedFilteredLabVisits($params, $request);
       
        $loadTransformer = $this->investigationService->getConsultedVisitsLabTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadInpatientsLabTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->investigationService->getInpatientLabRequests($params, $request);
       
        $loadTransformer = $this->investigationService->getLabTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function loadOutpatientsLabTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->investigationService->getOutpatientLabRequests($params, $request);
       
        $loadTransformer = $this->investigationService->getLabTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function createLabResult(SaveLabResultRequest $request, Prescription $prescription)
    {
        return $this->investigationService->createLabResultRecord($request, $prescription, $request->user());
    }

    public function updateLabResult(SaveLabResultRequest $request, Prescription $prescription)
    {
        return $this->investigationService->updateLabResultRecord($request, $prescription, $request->user());
    }

    public function removeLabResult(Prescription $prescription)
    {
        return $this->investigationService->removeLabResultRecord($prescription);
    }

    public function edit(Prescription $prescription)
    {
        return new InvestigationResultResource($prescription);
    }

    public function getAllTestsAndResults(Request $request, Prescription $prescription)
    {
        $prescriptions = $this->investigationService->getAllPatientsVisitsTests($prescription->visit);

        return ['tests' => New PrintLabTestsCollection($prescriptions)];
    }
}
