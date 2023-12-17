<?php

namespace App\Http\Controllers;

use App\Models\Nurse;
use App\Services\DatatablesService;
use App\Services\NurseService;
use Illuminate\Http\Request;

class NurseController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly NurseService $nurseService)
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('nurses.nurses');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function loadRegularVisitsNurses(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->nurseService->getPaginatedRegularConsultedVisitsNurses($params);
       
        $loadTransformer = $this->nurseService->getConsultedVisitsNursesTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadAncVisitsNurses(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->nurseService->getPaginatedAncConsultedVisitsNurses($params);
       
        $loadTransformer = $this->nurseService->getConsultedVisitsNursesTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }
}
