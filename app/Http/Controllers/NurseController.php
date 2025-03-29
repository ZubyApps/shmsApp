<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\DatatablesService;
use App\Services\NurseService;
use App\Services\ResourceService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NurseController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly NurseService $nurseService,
        private readonly UserService $userService,
        private readonly ResourceService $resourceService,
        private readonly ThirdPartyController $thirdPartyController,
        private readonly WardController $wardController
        )
    {
        
    }

    public function index()
    {
        return view('nurses.nurses', [
            'doctors' => $this->userService->listStaff(designation: 'Doctor'),
            'thirdParties'  => $this->thirdPartyController->showAll('id', 'short_name'),
            'wards'         => $this->wardController->showAll('id', 'short_name', 'long_name', 'bed_number', 'visit_id'),
            'feverBenchMark' => Cache::get('feverBenchmark', 37.3),
        ]);
    }

    public function loadVisitsNurses(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->nurseService->getpaginatedFilteredNurseVisits($params, $request);
       
        $loadTransformer = $this->nurseService->getConsultedVisitsNursesTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function emergencyList(Request $request)
    {
        $resources = $this->resourceService->getEmergencyList($request);

        $listTransformer = $this->resourceService->listTransformer1();

        return array_map($listTransformer, (array)$resources->getIterator());

    }

    public function nurseDone(Request $request, Visit $visit)
    {
        return $this->nurseService->done($visit, $request->user());
    }
}
