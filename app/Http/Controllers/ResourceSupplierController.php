<?php

namespace App\Http\Controllers;

use App\Models\ResourceSupplier;
use App\Http\Requests\StoreResourceSupplierRequest;
use App\Http\Requests\UpdateResourceSupplierRequest;
use App\Http\Resources\ResourceSupplierResource;
use App\Services\DatatablesService;
use App\Services\ResourceSupplierService;
use Illuminate\Http\Request;

class ResourceSupplierController extends Controller
{

    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ResourceSupplierService $resourceSupplierService,
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
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreResourceSupplierRequest $request)
    {
        $resourceSupplier = $this->resourceSupplierService->create($request, $request->user());

        return $resourceSupplier->load('user');
    }

    public function list(Request $request)
    {   
        return $this->resourceSupplierService->getSupplierList($request);
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->resourceSupplierService->getPaginatedResourceSupplierStocks($params);
       
        $loadTransformer = $this->resourceSupplierService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    /**
     * Display the specified resource.
     */
    public function show(ResourceSupplier $resourceSupplier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResourceSupplier $resourceSupplier)
    {
        return new ResourceSupplierResource($resourceSupplier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResourceSupplierRequest $request, ResourceSupplier $resourceSupplier)
    {
        return $this->resourceSupplierService->update($request, $resourceSupplier, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceSupplier $resourceSupplier)
    {
        return $resourceSupplier->destroy($resourceSupplier->id);
    }
}
