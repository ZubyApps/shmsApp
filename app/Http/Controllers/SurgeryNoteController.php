<?php

namespace App\Http\Controllers;

use App\Models\SurgeryNote;
use App\Http\Requests\StoreSurgeryNoteRequest;
use App\Http\Requests\UpdateSurgeryNoteRequest;
use App\Http\Resources\SurgeryNoteResource;
use App\Services\DatatablesService;
use App\Services\SurgeryNoteService;
use Illuminate\Http\Request;

class SurgeryNoteController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly SurgeryNoteService $surgeryNoteService
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
    public function store(StoreSurgeryNoteRequest $request)
    {
        $registeration = $this->surgeryNoteService->create($request, $request->user());
        
        return $registeration;
    }

    /**
     * Display the specified resource.
     */
    public function show(SurgeryNote $surgeryNote)
    {
        //
    }


    public function loadSurgeryNoteTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->surgeryNoteService->getSurgeryNotes($params, $request);
       
        $loadTransformer = $this->surgeryNoteService->getSurgeryNoteTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SurgeryNote $surgeryNote)
    {
       return new SurgeryNoteResource($surgeryNote);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSurgeryNoteRequest $request, SurgeryNote $surgeryNote)
    {
        return $this->surgeryNoteService->update($request, $surgeryNote, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SurgeryNote $surgeryNote)
    {
        return $surgeryNote->destroy($surgeryNote->id);
    }
}
