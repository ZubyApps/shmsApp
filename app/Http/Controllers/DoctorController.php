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

    public function loadUserRegularVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->doctorService->getPaginatedUserRegularConsultedVisits($params, $request->user());
       
        $loadTransformer = $this->doctorService->getUserConsultedVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadUserAncVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->doctorService->getPaginatedUserAncConsultedVisits($params, $request->user());
       
        $loadTransformer = $this->doctorService->getUserConsultedVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function consult(Visit $visit, Request $request)
    {
        return $this->doctorService->initiateConsultation($visit, $request);
    }
}
