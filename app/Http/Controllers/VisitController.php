<?php

namespace App\Http\Controllers;

use App\Models\Visit;
use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Http\Requests\VerifyPatientRequest;
use App\Services\DatatablesService;
use App\Services\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VisitController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly VisitService $visitService)
    {
        
    }

    public function storeVisit(StoreVisitRequest $request)
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

        $visits = $this->visitService->getPaginatedAllConsultedVisits($params, $request);
       
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
    
    

    /**
     * close a completed visit
     */
    public function closeVisit(Request $request, Visit $visit)
    {
        return DB::transaction(function () use($visit){
                $visit->update(['closed' => true]);
                $visit->patient()->update(['is_active' => false]);
            });

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit)
    {
       return DB::transaction(function() use($visit){
                $visit->destroy($visit->id);
                $visit->patient()->update(['is_active' => false]);
            });
    }
}
