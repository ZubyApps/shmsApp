<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Services\DatatablesService;
use App\Services\DoctorService;
use App\Services\ResourceService;
use App\Services\UserService;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly DoctorService $doctorService,
        private readonly ResourceService $resourceService,
        private readonly ThirdPartyController $thirdPartyController,
        private readonly UserService $userService,
        private readonly WardController $wardController
        )
    {
        
    }

    public function index()
    {
        return view('doctors.doctors', [
            'doctors'       => $this->userService->listStaff(designation: 'Doctor'),
            'thirdParties'  => $this->thirdPartyController->showAll('id', 'short_name'),
            'wards'         => $this->wardController->showAll('id', 'short_name', 'long_name', 'bed_number', 'visit_id')
        ]);
    }

    public function list(Request $request)
    {
        $resources = $this->resourceService->getFormattedList($request);

        $listTransformer = $this->resourceService->listTransformer();

        return array_map($listTransformer, (array)$resources->getIterator());

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

    public function review(Visit $visit, Request $request)
    {
        return $this->doctorService->initiateReview($visit, $request);
    }
}
