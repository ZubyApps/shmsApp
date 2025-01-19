<?php

namespace App\Http\Controllers;

use App\Models\Procedure;
use App\Http\Requests\StoreProcedureRequest;
use App\Http\Requests\UpdateProcedureRequest;
use App\Http\Resources\ProcedureResource;
use App\Services\DatatablesService;
use App\Services\ProcedureService;
use Illuminate\Http\Request;

class ProcedureController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ProcedureService $procedureService,
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
    public function store(StoreProcedureRequest $request)
    {
        //
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $procedures = $this->procedureService->getPaginatedProcedures($params, $request);
       
        $loadTransformer = $this->procedureService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $procedures, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Procedure $procedure)
    {
        return new ProcedureResource($procedure);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProcedureRequest $request, Procedure $procedure)
    {
        return $this->procedureService->update($request, $procedure, $request->user());
    }

    public function updateStatus(UpdateProcedureRequest $request, Procedure $procedure)
    {
        return $this->procedureService->updateStatus($request, $procedure, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Procedure $procedure)
    {
        return $procedure->destroy($procedure->id);
    }
}
