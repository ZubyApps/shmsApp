<?php

namespace App\Http\Controllers;

use App\Models\ResourceStockDate;
use App\Http\Requests\StoreResourceStockDateRequest;
use App\Http\Requests\UpdateResourceStockDateRequest;
use App\Http\Resources\ResourceStockDateResource;
use App\Services\DatatablesService;
use App\Services\ResourceStockDateService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResourceStockDateController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ResourceStockDateService $resourceStockDateService,
        private readonly ResourceController $resourceController
        )
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
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResourceStockDateRequest $request)
    {
        $date = $this->resourceStockDateService->create($request, $request->user());

        return $date->load('user');
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->resourceStockDateService->getPaginatedResourceStockDates($params);
       
        $loadTransformer = $this->resourceStockDateService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function processReset(ResourceStockDate $resourceStockDate)
    {
        DB::transaction(function () use($resourceStockDate) {
            $this->resourceController->resetStock();

            return $resourceStockDate->update(['reset' => true]);
        });
        
    }


    /**
     * Display the specified resource.
     */
    public function show(ResourceStockDate $resourceStockDate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResourceStockDate $resourceStockDate)
    {
        return new ResourceStockDateResource($resourceStockDate);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResourceStockDateRequest $request, ResourceStockDate $resourceStockDate)
    {
        return $this->resourceStockDateService->update($request, $resourceStockDate, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceStockDate $resourceStockDate)
    {
        return $resourceStockDate->destroy($resourceStockDate->id);
    }
}
