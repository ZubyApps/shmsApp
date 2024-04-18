<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeSponsorRequest;
use App\Http\Requests\CloseVisitRequest;
use App\Http\Requests\OpenVisitRequest;
use App\Models\Visit;
use App\Http\Requests\StoreVisitRequest;
use App\Models\Patient;
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

    public function storeVisit(StoreVisitRequest $request, Patient $patient)
    {
        return $this->visitService->create($request, $patient, $request->user());
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
    
    public function closeVisit(CloseVisitRequest $request, Visit $visit)
    {
       return $this->visitService->close($request->user(), $visit);
    }

    public function openVisit(OpenVisitRequest $request, Visit $visit)
    {
       return $this->visitService->open($request->user(), $visit);
    }

    public function destroy(Visit $visit)
    {
       return $this->visitService->delete($visit);
    }
}
