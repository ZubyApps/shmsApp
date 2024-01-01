<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Visit;
use App\Services\DatatablesService;
use App\Services\DoctorService;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly DoctorService $doctorService)
    {
        
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('doctors.doctors');
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

    public function loadOutPatientVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->doctorService->getPaginatedOutpatientConsultedVisits($request, $params, $request->user());
       
        $loadTransformer = $this->doctorService->getConsultedVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadInPatientVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->doctorService->getPaginatedInpatientConsultedVisits($request, $params, $request->user());
       
        $loadTransformer = $this->doctorService->getConsultedVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadAncPatientVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->doctorService->getPaginatedAncConsultedVisits($request, $params, $request->user());
       
        $loadTransformer = $this->doctorService->getConsultedVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function consult(Visit $visit, Request $request)
    {
        return $this->doctorService->initiateConsultation($visit, $request);
    }
}
