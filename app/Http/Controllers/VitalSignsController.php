<?php

namespace App\Http\Controllers;

use App\Models\VitalSigns;
use App\Http\Requests\StoreVitalSignsRequest;
use App\Http\Requests\UpdateVitalSignsRequest;
use App\Services\DatatablesService;
use App\Services\VitalSignsService;
use Illuminate\Http\Request;

class VitalSignsController extends Controller
{
    public function  __construct(
        private readonly DatatablesService $datatablesService,
        private readonly VitalSignsService $vitalSignsService)
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
    public function store(StoreVitalSignsRequest $request)
    {
        $vitalSigns = $this->vitalSignsService->create($request, $request->user());
        
        return $vitalSigns->load('visit');
    }

    /**
     * Display the specified resource.
     */
    public function show(VitalSigns $vitalSigns)
    {
        //
    }

    public function loadVitalSignsTableByVisit(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $vitalSigns = $this->vitalSignsService->getPaginatedVitalSignsByVisit($params, $request);
       
        $loadTransformer = $this->vitalSignsService->getVitalSignsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $vitalSigns, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(VitalSigns $vitalSigns)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVitalSignsRequest $request, VitalSigns $vitalSigns)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(VitalSigns $vitalSigns)
    {
        return $vitalSigns->destroy($vitalSigns->id);
    }
}
