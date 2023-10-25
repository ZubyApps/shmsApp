<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorCategoryRequest;
use App\Http\Requests\UpdateSponsorCategoryRequest;
use App\Http\Resources\SponsorCategoryResource;
use App\Models\SponsorCategory;
use App\Services\DatatablesService;
use App\Services\SponsorCategoryService;
use Illuminate\Http\Request;

class SponsorCategoryController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly SponsorCategoryService $sponsorCategoryService)
    {
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSponsorCategoryRequest $request)
    {
        $sponsorCategory = $this->sponsorCategoryService->create($request, $request->user());

        return $sponsorCategory;
    }

    /**
     * Display all the resource by requested columns.
     */
    public function showAll(string ...$columns)
    {
        return SponsorCategory::all($columns);   
    }

    /**
     * Display all the resource's relationship by the id given.
     */
    public function list(Request $request, SponsorCategory $sponsorCategory)
    {   
        return $sponsorCategory->sponsors()->orderBy('name')->get(['id', 'name'])->toJson();
    }

    /**
     * Display all the resource.
     */
    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->sponsorCategoryService->getPaginatedSponsorCategories($params);
       
        $loadTransformer = $this->sponsorCategoryService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SponsorCategory $sponsorCategory)
    {
        return new SponsorCategoryResource($sponsorCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSponsorCategoryRequest $request, SponsorCategory $sponsorCategory)
    {        
        return $this->sponsorCategoryService->update($request, $sponsorCategory, $request->user());

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SponsorCategory $sponsorCategory)
    {
        return $sponsorCategory->destroy($sponsorCategory->id);
    }
}
