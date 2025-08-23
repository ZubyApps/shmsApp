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
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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

    public function reviewVisit(Request $request, Visit $visit)
    {
       return $this->visitService->review($request, $visit);
    }

    public function resolveVisit(Visit $visit)
    {
       return $this->visitService->resolve($visit);
    }

    public function getPatientAverageWaitingTime()
    {
        if (Cache::get("lastWeek")){
            $averageWaitingTime = Cache::getMultiple(["lastWeek", "thisWeek", "lastMonth", "thisMonth"]);
            return response()->json(
                [
                    "lastWeek" => $averageWaitingTime["lastWeek"], 
                    "thisWeek" => $averageWaitingTime["thisWeek"], 
                    "lastMonth" => $averageWaitingTime["lastMonth"], 
                    "thisMonth" => $averageWaitingTime["thisMonth"]
                ]
            );
        }

        $day        = new CarbonImmutable();
        $lastWeek   = $this->visitService->averageWaitingTime($day->subWeek(), 'startOfWeek', 'endOfWeek');
        $thisWeek   = $this->visitService->averageWaitingTime($day, 'startOfWeek', 'endOfWeek');
        $lastMonth  = $this->visitService->averageWaitingTime($day->subMonth(), 'startOfMonth', 'endOfMonth');
        $thisMonth  = $this->visitService->averageWaitingTime($day, 'startOfMonth', 'endOfMonth');

        Cache::putMany(["lastWeek" => $lastWeek, "thisWeek" => $thisWeek, "lastMonth" => $lastMonth, "thisMonth" => $thisMonth], now()->addHours(2));

        return response()->json(["lastWeek" => $lastWeek, "thisWeek" => $thisWeek, "lastMonth" => $lastMonth, "thisMonth" => $thisMonth]);
    }

    public function destroy(Visit $visit)
    {
       return $this->visitService->delete($visit);
    }
}
