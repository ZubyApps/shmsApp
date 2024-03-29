<?php

namespace App\Http\Controllers;

use App\Models\ExpenseCategory;
use App\Http\Requests\StoreExpenseCategoryRequest;
use App\Http\Requests\UpdateExpenseCategoryRequest;
use App\Http\Resources\ExpenseCategoryResource;
use App\Services\DatatablesService;
use App\Services\ExpenseCategoryService;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ExpenseCategoryService $expenseCategoryService)
    {
        
    }

    public function showAll(string ...$columns)
    {
        return ExpenseCategory::all($columns);
    }

    public function store(StoreExpenseCategoryRequest $request)
    {
        return $this->expenseCategoryService->create($request, $request->user());
    }

    public function loadExpenseCategories(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->expenseCategoryService->getPaginatedExpenseCategories($params, $request);
       
        $loadTransformer = $this->expenseCategoryService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        return new ExpenseCategoryResource($expenseCategory);
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory)
    {
        return $this->expenseCategoryService->update($request, $expenseCategory, $request->user());
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        return $expenseCategory->destroy($expenseCategory->id);
    }
}
