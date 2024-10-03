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
        private readonly ResourceCategoryController $resourceCategoryController,
        private readonly UnitDescriptionController $unitDescriptionController,
        private readonly MarkedForController $markedForController,
        )
    {
        
    }

    public function index()
    {
        return view('resources.resources',  [
            'categories'        => $this->resourceCategoryController->showAll('id', 'name'),
            'markedFors'        => $this->markedForController->showAll('id', 'name'),
            'unitDescriptions'  => $this->unitDescriptionController->showAll('id', 'long_name', 'short_name')
        ]);
    }

    public function list(Request $request)
    {
        $resources = $this->resourceService->getFormattedList($request);

        $listTransformer = $this->resourceService->listTransformer();

        return array_map($listTransformer, (array)$resources->getIterator());

    }

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

    public function toggleIsActive(Request $request, Resource $resource)
    {
        $resource->update([
            'is_active' => !$resource->is_active
        ]);
    }

    public function edit(Resource $resource)
    {
        return new ResourceResource($resource);
    }

    public function theatreMatch(Request $request)
    {
        $resources = $this->resourceService->getTheatreMarch($request);

        $listTransformer = $this->resourceService->listTransformer1();

        return array_map($listTransformer, (array)$resources->getIterator());

    }

    public function resetStock()
    {
        $resetStock = Resource::all();

        foreach ($resetStock as $stock) {
            $stock->stock_level = null;
            $stock->save();
        }

        return response();
    }

    public function update(UpdateResourceRequest $request, Resource $resource)
    {
        return $this->resourceService->update($request, $resource, $request->user());
    }

    public function destroy(Resource $resource)
    {
        return $resource->destroy($resource->id);
    }
}
