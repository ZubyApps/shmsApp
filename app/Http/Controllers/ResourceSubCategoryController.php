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
    public function store(StoreResourceSubCategoryRequest $request)
    {
        $resourceSubCategory = $this->resourceSubCategoryService->create($request, $request->user());

        return $resourceSubCategory->load('user');
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->resourceSubCategoryService->getPaginatedResourceCategories($params);
       
        $loadTransformer = $this->resourceSubCategoryService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    /**
     * Display the specified resource.
     */
    public function show(ResourceSubCategory $resourceSubCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResourceSubCategory $resourceSubCategory)
    {
        return new ResourceSubCategoryResource($resourceSubCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResourceSubCategoryRequest $request, ResourceSubCategory $resourceSubCategory)
    {
        return $this->resourceSubCategoryService->update($request, $resourceSubCategory, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceSubCategory $resourceSubCategory)
    {
        return $resourceSubCategory->destroy($resourceSubCategory->id);
    }
}
