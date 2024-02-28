<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeSponsorRequest;
use App\Http\Requests\CloseVisitRequest;
use App\Http\Requests\OpenVisitRequest;
use App\Models\Visit;
use App\Http\Requests\StoreVisitRequest;
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

    public function storeVisit(StoreVisitRequest $request)
    {
        $visit = $this->visitService->create($request, $request->user());
        
        return $visit;
    }

    public function loadWaitingTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getPaginatedWaitingVisits($params);
       
        $loadTransformer = $this->visitService->getWaitingListTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function changeSponsor(ChangeSponsorRequest $request, Visit $visit)
    {
        return $this->visitService->changeVisitSponsor($request, $visit, $request->user());
    }

    public function dischargePatient(Request $request, Visit $visit)
    {
        return $this->visitService->discharge($request, $visit, $request->user());
    }
    
    /**
     * close a completed visit
     */
    public function closeVisit(CloseVisitRequest $request, Visit $visit)
    {
       return $this->visitService->close($request->user(), $visit);
    }

    /**
     * open a close visit
     */
    public function openVisit(OpenVisitRequest $request, Visit $visit)
    {
       return $this->visitService->open($request->user(), $visit);
    }

    /**
     * delete a specified visit.
     */
    public function destroy(Visit $visit)
    {
       return $this->visitService->delete($visit);
    }
}
