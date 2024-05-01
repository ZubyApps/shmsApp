<?php

namespace App\Http\Controllers;

use App\Models\MedicationCategory;
use App\Http\Requests\StoreMedicationCategoryRequest;
use App\Http\Requests\UpdateMedicationCategoryRequest;
use App\Http\Resources\MedicationCategoryResource;
use App\Services\DatatablesService;
use App\Services\MedicationCategoryService;
use Illuminate\Http\Request;

class MedicationCategoryController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly MedicationCategoryService $medicationCategoryService
        )
    {
        
    }

    public function store(StoreMedicationCategoryRequest $request)
    {
        return $this->medicationCategoryService->create($request, $request->user());
    }

    public function list()
    {   
        return $this->medicationCategoryService->getList();
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $categories = $this->medicationCategoryService->getPaginatedResourceCategories($params);
       
        $loadTransformer = $this->medicationCategoryService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $categories, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicationCategory $medicationCategory)
    {
        return new MedicationCategoryResource($medicationCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicationCategoryRequest $request, MedicationCategory $medicationCategory)
    {
        return $this->medicationCategoryService->update($request, $medicationCategory, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicationCategory $medicationCategory)
    {
        return $medicationCategory->destroy($medicationCategory->id);
    }
}
