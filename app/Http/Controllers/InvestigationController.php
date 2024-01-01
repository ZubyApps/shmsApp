<?php

namespace App\Http\Controllers;

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

        $sponsors = $this->investigationService->getPaginatedLabRequests($params, $request);
       
        $loadTransformer = $this->investigationService->getLabTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }
}
