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

    public function loadVisitsTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getPaginatedConsultedVisits($params);
       
        $loadTransformer = $this->visitService->getConsultedVisitsTransformer();

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
