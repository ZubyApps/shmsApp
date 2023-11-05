<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Http\Requests\StoreResourceRequest;
use App\Http\Requests\UpdateResourceRequest;
use App\Http\Resources\ResourceResource;
use App\Services\DatatablesService;
use App\Services\ResourceService;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ResourceService $resourceService,
        private readonly ResourceCategoryController $resourceCategoryController
        )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('resources.resources',  ['categories' =>$this->resourceCategoryController->showAll('id', 'name')]);
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
    public function store(StoreResourceRequest $request)
    {
        $resource = $this->resourceService->create($request, $request->user());

        return $resource->load('user');
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->resourceService->getPaginatedResources($params);
       
        $loadTransformer = $this->resourceService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    /**
     * Display the specified resource.
     */
    public function toggleIsActive(Request $request, Resource $resource)
    {
        $resource->update([
            'is_active' => !$resource->is_active
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Resource $resource)
    {
        return new ResourceResource($resource);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResourceRequest $request, Resource $resource)
    {
        return $this->resourceService->update($request, $resource, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource)
    {
        return $resource->destroy($resource->id);
    }
}