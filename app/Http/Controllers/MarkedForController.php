<?php

namespace App\Http\Controllers;

use App\Models\MarkedFor;
use App\Http\Requests\StoreMarkedForRequest;
use App\Http\Requests\UpdateMarkedForRequest;
use App\Http\Resources\MarkedForResource;
use App\Services\DatatablesService;
use App\Services\MarkedForService;
use Illuminate\Http\Request;

class MarkedForController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly MarkedForService $markedForService)
    {
    }


    public function store(StoreMarkedForRequest $request)
    {
        $markedForService = $this->markedForService->create($request, $request->user());

        return $markedForService;
    }

    public function showAll(string ...$columns)
    {
        return MarkedFor::all($columns)->sortBy('name');
    }

    // public function selectOne($searchTerm)
    // {
    //     return $this->markedForService->getOne($searchTerm);
    // }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->markedForService->getPaginatedMarkedFors($params);
       
        $loadTransformer = $this->markedForService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MarkedFor $markedFor)
    {
        return new MarkedForResource($markedFor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMarkedForRequest $request, MarkedFor $markedFor)
    {
        return $this->markedForService->update($request, $markedFor, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MarkedFor $markedFor)
    {
        return $markedFor->destroy($markedFor->id);
    }
}
