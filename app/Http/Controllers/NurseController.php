<?php

namespace App\Http\Controllers;

use App\Models\Nurse;
use App\Services\DatatablesService;
use App\Services\NurseService;
use App\Services\UserService;
use Illuminate\Http\Request;

class NurseController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly NurseService $nurseService,
        private readonly UserService $userService
        )
    {
        
    }

    public function index()
    {
        return view('nurses.nurses', ['doctors' => $this->userService->listStaff('Doctor')]);
    }

    public function loadVisitsNurses(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->nurseService->getpaginatedFilteredNurseVisits($params, $request);
       
        $loadTransformer = $this->nurseService->getConsultedVisitsNursesTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }
}
