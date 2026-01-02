<?php

namespace App\Http\Controllers;

use App\Models\WalkIn;
use Illuminate\Http\Request;
use App\Services\WalkInService;
use App\Services\ResourceService;
use App\Services\PayMethodService;
use App\Services\DatatablesService;
use App\Http\Resources\WalkInResource;
use App\Http\Requests\StoreWalkInsRequest;
use App\Http\Requests\UpdateWalkInsRequest;
use App\Http\Resources\PrintLabTestsCollection;
use App\Models\Prescription;
use App\Models\Visit;

class WalkInController extends Controller
{
    public function __construct(
        private readonly WalkInService $walkInService,
        private readonly DatatablesService $datatablesService,
        private readonly ResourceService $resourceService,
        private readonly PayMethodService $payMethodService,
        )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('walkins.walkins', [
            'payMethods'    => $this->payMethodService->list(collection:true),
            // 'categories' => $this->sponsorCategoryController->showAll('id', 'name'),
            // 'doctors' => $this->userService->listStaff(designation: 'Doctor'),
            // 'feverBenchMark' => Cache::get('feverBenchmark', 37.3)
            ]
        );
    }

    public function listRequests(Request $request)
    {
        $items = $this->resourceService->getRequestsList($request);

        $listTransformer = $this->resourceService->listTransformer1();

        return array_map($listTransformer, (array)$items->getIterator());

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWalkInsRequest $request)
    {
        return $this->walkInService->create($request, $request->user());
    }

    /**
     * Display the specified resource.
     */
    public function loadWalkinTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $walkIns = $this->walkInService->getPaginatedWalkIns($params, $request);
       
        $loadTransformer = $this->walkInService->getLoadTransformer(auth()->user());

        return $this->datatablesService->datatableResponse($loadTransformer, $walkIns, $params); 
    }

    public function loadBillSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $billSummary = $this->walkInService->getWalkinBillSummaryTable($request);

        return response()->json([
            'data' => $billSummary,
            'draw' => $params->draw,
            'recordsTotal' => count($billSummary),
            'recordsFiltered' => count($billSummary)
        ]);
    }

    public function linkToVisit(WalkIn $walkIn, Visit $visit)
    {
        return $this->walkInService->handleVisitLink($walkIn, $visit);
    }

    public function unLinkVisit(WalkIn $walkIn)
    {
        return $this->walkInService->handleUnlinkVisit($walkIn);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WalkIn $walkIn)
    {
        return new WalkInResource($walkIn);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateWalkInsRequest $request, WalkIn $walkIn)
    {
        return $this->walkInService->update($request, $walkIn, $request->user());
    }

    public function getAllTestsAndResults(Prescription $prescription)
    {
        $prescriptions = $this->walkInService->getAllWalkInTests($prescription->walkIn);

        return ['tests' => New PrintLabTestsCollection($prescriptions)];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WalkIn $walkIn)
    {
        return $walkIn->destroy($walkIn->id);
    }
}
