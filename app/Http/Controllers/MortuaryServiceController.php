<?php

namespace App\Http\Controllers;

use App\Models\MortuaryService;
use App\Services\ResourceService;
use App\Services\PayMethodService;
use App\Services\DatatablesService;
use App\Http\Requests\StoreMortuaryServiceRequest;
use App\Http\Requests\UpdateMortuaryServiceRequest;
use App\Http\Resources\MortuaryServiceResource;
use App\Services\MortuaryServiceService;
use Illuminate\Http\Request;

class MortuaryServiceController extends Controller
{
    public function __construct(
        private readonly MortuaryServiceService $mortuaryServiceService,
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
        return view('mortuaryservices.mortuaryservices', [
            'payMethods'    => $this->payMethodService->list(collection:true),
            // 'categories' => $this->sponsorCategoryController->showAll('id', 'name'),
            // 'doctors' => $this->userService->listStaff(designation: 'Doctor'),
            // 'feverBenchMark' => Cache::get('feverBenchmark', 37.3)
            ]
        );
    }

    public function listRequests(Request $request)
    {
        $items = $this->resourceService->getHospitalServicesList($request);

        $listTransformer = $this->resourceService->listTransformer1();

        return array_map($listTransformer, (array)$items->getIterator());

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMortuaryServiceRequest $request)
    {
        return $this->mortuaryServiceService->create($request, $request->user());
    }

    public function loadMortuaryTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $walkIns = $this->mortuaryServiceService->getPaginatedMortuaryServices($params, $request);
       
        $loadTransformer = $this->mortuaryServiceService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $walkIns, $params); 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MortuaryService $mortuaryService)
    {
        return new MortuaryServiceResource($mortuaryService);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMortuaryServiceRequest $request, MortuaryService $mortuaryService)
    {
        return $this->mortuaryServiceService->update($request, $mortuaryService, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MortuaryService $mortuaryService)
    {
        return $mortuaryService->destroy($mortuaryService->id);
    }

    public function fillDate (Request $request, MortuaryService $mortuaryService)
    {
        return $this->mortuaryServiceService->fillDateCollected($request, $mortuaryService, $request->user());
    }
}
