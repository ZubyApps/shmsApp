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
    
    public function store(StoreSponsorCategoryRequest $request)
    {
        $sponsorCategory = $this->sponsorCategoryService->create($request, $request->user());

        return $sponsorCategory;
    }

    public function showAll(string ...$columns)
    {
        return SponsorCategory::all($columns)->load('sponsors');
    }

    public function list(SponsorCategory $sponsorCategory)
    {   
        return $sponsorCategory->sponsors()->orderBy('name')->get(['id', 'name'])->toJson();
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->sponsorCategoryService->getPaginatedSponsorCategories($params);
       
        $loadTransformer = $this->sponsorCategoryService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }
    
    public function edit(SponsorCategory $sponsorCategory)
    {
        return new SponsorCategoryResource($sponsorCategory);
    }

    public function update(UpdateSponsorCategoryRequest $request, SponsorCategory $sponsorCategory)
    {        
        return $this->sponsorCategoryService->update($request, $sponsorCategory, $request->user());

    }

    public function destroy(SponsorCategory $sponsorCategory)
    {
        return $sponsorCategory->destroy($sponsorCategory->id);
    }
}
