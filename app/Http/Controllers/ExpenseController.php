<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Services\DatatablesService;
use App\Services\ExpenseService;

class ExpenseController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ExpenseService $expenseService)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        return $this->expenseService->create($request, $request->user());
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return new ExpenseResource($expense);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        return $this->expenseService->update($request, $expense, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        return $expense->destroy($expense->id);
    }
}
