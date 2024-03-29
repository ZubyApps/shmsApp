<?php

namespace App\Http\Controllers;

use App\Models\ResourceCategory;
use App\Http\Requests\StoreResourceCategoryRequest;
use App\Http\Requests\UpdateResourceCategoryRequest;
use App\Http\Resources\ResourceCategoryResource;
use App\Services\DatatablesService;
use App\Services\ResourceCategoryService;
use Illuminate\Http\Request;

class ResourceCategoryController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ResourceCategoryService $resourceCategoryService
        )
    {
        
    }

    public function showAll(string ...$columns)
    {
        return ResourceCategory::all($columns);
    }

    public function list(Request $request, ResourceCategory $resourceCategory)
    {   
        return $resourceCategory->resourceSubCategories()->orderBy('name')->get(['id', 'name'])->toJson();
    }

    public function store(StoreResourceCategoryRequest $request)
    {
        return $this->resourceCategoryService->create($request, $request->user());
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $categories = $this->resourceCategoryService->getPaginatedResourceCategories($params);
       
        $loadTransformer = $this->resourceCategoryService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $categories, $params);  
    }

    public function edit(ResourceCategory $resourceCategory)
    {
        return new ResourceCategoryResource($resourceCategory);
    }

    public function update(UpdateResourceCategoryRequest $request, ResourceCategory $resourceCategory)
    {
        return $this->resourceCategoryService->update($request, $resourceCategory, $request->user());
    }

    public function destroy(ResourceCategory $resourceCategory)
    {
        return $resourceCategory->destroy($resourceCategory->id);
    }
}
