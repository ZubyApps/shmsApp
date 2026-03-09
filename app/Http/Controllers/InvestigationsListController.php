<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvestigationsListRequest;
// use App\Http\Requests\UpdateInvestigationsListRequest;
use App\Models\InvestigationsList;
use App\Services\DatatablesService;
use App\Services\InvestigationsListService;
use Illuminate\Http\Request;

class InvestigationsListController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly InvestigationsListService $investigationsListService,
        )
    {
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
    public function store(StoreInvestigationsListRequest $request)
    {
        return $this->investigationsListService->create($request, $request->user());
    }

    public function loadInvestigationsListTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $procedures = $this->investigationsListService->getPaginatedList($params, $request);
       
        $loadTransformer = $this->investigationsListService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $procedures, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InvestigationsList $investigationsList)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function voidEntry(InvestigationsList $investigationsList)
    {
        $this->investigationsListService->voidListEntry($investigationsList);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InvestigationsList $investigationsList)
    {
        return $investigationsList->destroy($investigationsList->id);
    }
}
