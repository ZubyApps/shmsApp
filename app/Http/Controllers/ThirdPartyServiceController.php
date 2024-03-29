<?php

namespace App\Http\Controllers;

use App\Models\ThirdPartyService;
use App\Http\Requests\StoreThirdPartyServiceRequest;
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

    public function index()
    {
        return view('thirdpartyservices.thirdPartyServices');
    }

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

    public function destroy(ThirdPartyService $thirdPartyService)
    {
        return $thirdPartyService->destroy($thirdPartyService->id);
    }
}
