<?php

namespace App\Http\Controllers;

use App\Models\Ward;
use App\Services\WardService;
use App\Services\DatatablesService;
use App\Http\Requests\StoreWardRequest;
use App\Http\Requests\UpdateWardRequest;
use App\Http\Resources\WardResource;
use Illuminate\Http\Request;

class WardController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly WardService $wardService)
    {
    }
    
    public function store(StoreWardRequest $request)
    {
        $wardService = $this->wardService->create($request, $request->user());

        return $wardService;
    }

    public function showAll(string ...$columns)
    {
        return Ward::all($columns);
    }

    public function list(Request $request)
    {
        $resources = $this->wardService->getFormattedList($request);

        $listTransformer = $this->wardService->listTransformer();

        return array_map($listTransformer, (array)$resources->getIterator());

    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->wardService->getPaginatedWards($params);
       
        $loadTransformer = $this->wardService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }
    
    public function edit(Ward $ward)
    {
        return new WardResource($ward);
    }

    public function update(UpdateWardRequest $request, Ward $ward)
    {        
        return $this->wardService->update($request, $ward, $request->user());
    }

    public function clear(Ward $ward)
    {        
        return $this->wardService->clearWard($ward);
    }

    public function updateAll(Ward $ward)
    {
        return $this->wardService->updateAllWards($ward);
    }

    public function destroy(Ward $ward)
    {
        return $ward->destroy($ward->id);
    }
}
