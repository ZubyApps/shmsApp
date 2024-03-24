<?php

namespace App\Http\Controllers;

use App\Models\ThirdPartyService;
use App\Http\Requests\StoreThirdPartyServiceRequest;
use App\Http\Requests\UpdateThirdPartyServiceRequest;
use App\Models\Prescription;
use App\Services\DatatablesService;
use App\Services\ThirdPartyServicesService;
use Illuminate\Http\Request;

class ThirdPartyServiceController extends Controller
{
    public function __construct(
        private readonly SponsorCategoryController $sponsorCategoryController, 
        private readonly DatatablesService $datatablesService, 
        private readonly ThirdPartyServicesService $thirdPartyServicesService,
        )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('thirdpartyservices.thirdPartyServices');
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
    public function store(StoreThirdPartyServiceRequest $request, Prescription $prescription)
    {
        return $this->thirdPartyServicesService->create($request, $prescription, $request->user());
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->thirdPartyServicesService->getPaginatedThirdPartyServices($params);
       
        $loadTransformer = $this->thirdPartyServicesService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ThirdPartyService $thirdPartyService)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateThirdPartyServiceRequest $request, ThirdPartyService $thirdPartyService)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ThirdPartyService $thirdPartyService)
    {
        return $thirdPartyService->destroy($thirdPartyService->id);
    }
}
