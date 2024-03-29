<?php

namespace App\Http\Controllers;

use App\Models\ResourceSubCategory;
use App\Http\Requests\StoreResourceSubCategoryRequest;
use App\Http\Requests\UpdateResourceSubCategoryRequest;
use App\Http\Resources\ResourceSubCategoryResource;
use App\Services\DatatablesService;
use App\Services\ResourceSubCategoryService;
use Illuminate\Http\Request;

class ResourceSubCategoryController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ResourceSubCategoryService $resourceSubCategoryService,
        )
    {
        
    }

    public function store(StoreResourceSubCategoryRequest $request)
    {
        $resourceSubCategory = $this->resourceSubCategoryService->create($request, $request->user());

        return $resourceSubCategory;
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->resourceSubCategoryService->getPaginatedResourceCategories($params);
       
        $loadTransformer = $this->resourceSubCategoryService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function edit(ResourceSubCategory $resourceSubCategory)
    {
        return new ResourceSubCategoryResource($resourceSubCategory);
    }

    public function update(UpdateResourceSubCategoryRequest $request, ResourceSubCategory $resourceSubCategory)
    {
        return $this->resourceSubCategoryService->update($request, $resourceSubCategory, $request->user());
    }

    public function destroy(ResourceSubCategory $resourceSubCategory)
    {
        return $resourceSubCategory->destroy($resourceSubCategory->id);
    }
}
