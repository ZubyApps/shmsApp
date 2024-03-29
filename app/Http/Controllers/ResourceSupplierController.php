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

    public function edit(ResourceSupplier $resourceSupplier)
    {
        return new ResourceSupplierResource($resourceSupplier);
    }

    public function update(UpdateResourceSupplierRequest $request, ResourceSupplier $resourceSupplier)
    {
        return $this->resourceSupplierService->update($request, $resourceSupplier, $request->user());
    }

    public function destroy(ResourceSupplier $resourceSupplier)
    {
        return $resourceSupplier->destroy($resourceSupplier->id);
    }
}
