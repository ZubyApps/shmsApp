<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Services\DatatablesService;
use App\Services\VisitService;
use Illuminate\Http\Request;

class VisitController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly VisitService $visitService)
    {
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreVisitRequest $request)
    {
        $visit = $this->visitService->create($request, $request->user());
        
        return $visit->load('patient');
    }

    public function loadWaitingTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getPaginatedWaitingVisits($params);
       
        $loadTransformer = $this->visitService->getWaitingListTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadAllVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getPaginatedAllConsultedVisits($params);
       
        $loadTransformer = $this->visitService->getRegularConsultedVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadUserRegularVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getPaginatedUserRegularConsultedVisits($params, $request->user());
       
        $loadTransformer = $this->visitService->getRegularConsultedVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadInpatientsVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getPaginatedInpatientVisits($params);
       
        $loadTransformer = $this->visitService->getInpatientsVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadUserAncVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getPaginatedUserAncConsultedVisits($params, $request->user());
       
        $loadTransformer = $this->visitService->getAncConsultedVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadRegularVisitsNurses(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getPaginatedRegularConsultedVisitsNurses($params);
       
        $loadTransformer = $this->visitService->getConsultedVisitsNursesTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadAncVisitsNurses(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getPaginatedAncConsultedVisitsNurses($params);
       
        $loadTransformer = $this->visitService->getConsultedVisitsNursesTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }
    

    /**
     * Display the specified resource.
     */
    public function consult(Visit $visit, Request $request)
    {
        return $this->visitService->initiateConsultation($visit, $request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVisitRequest $request, Visit $visit)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
        $visit->destroy($visit->id);

       return $visit->patient()->update([
            'is_active' => false
        ]);
        
    }
}
