<?php

namespace App\Http\Controllers;

use App\Models\UnitDescription;
use App\Http\Requests\StoreUnitDescriptionRequest;
use App\Http\Requests\UpdateUnitDescriptionRequest;
use App\Http\Resources\UnitDescriptionResource;
use App\Services\DatatablesService;
use App\Services\UnitDescriptionService;
use Illuminate\Http\Request;

class UnitDescriptionController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly UnitDescriptionService $unitDescriptionService)
    {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUnitDescriptionRequest $request)
    {
        $unitDescription = $this->unitDescriptionService->create($request, $request->user());

        return $unitDescription;
    }

    public function showAll(string ...$columns)
    {
        return UnitDescription::all($columns);
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->unitDescriptionService->getPaginatedWards($params);
       
        $loadTransformer = $this->unitDescriptionService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnitDescription $unitDescription)
    {
        return new UnitDescriptionResource($unitDescription);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUnitDescriptionRequest $request, UnitDescription $unitDescription)
    {
        return $this->unitDescriptionService->update($request, $unitDescription, $request->user());
    }

    public function updateAll(UnitDescription $unitDescription)
    {
        return $this->unitDescriptionService->updateAllDescriptions($unitDescription);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UnitDescription $unitDescription)
    {
        return $unitDescription->destroy($unitDescription->id);
    }
}
