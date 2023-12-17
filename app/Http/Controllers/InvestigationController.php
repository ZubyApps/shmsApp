<?php

namespace App\Http\Controllers;

use App\Models\Investigation;
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function loadRegularVisitsLab(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->investigationService->getPaginatedRegularConsultedVisitsLab($params);
       
        $loadTransformer = $this->investigationService->getConsultedVisitsLabTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadAncVisitsLab(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->investigationService->getPaginatedAncConsultedVisitsLab($params);
       
        $loadTransformer = $this->investigationService->getConsultedVisitsLabTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadInpatientVisitsLab(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->investigationService->getPaginatedInpatientVisitsLab($params);
       
        $loadTransformer = $this->investigationService->getConsultedVisitsLabTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }
}
