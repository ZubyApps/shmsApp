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
    ){ 
    }

    public function store(StoreSurgeryNoteRequest $request)
    {     
        return $this->surgeryNoteService->create($request, $request->user());
    }

    public function loadSurgeryNoteTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->surgeryNoteService->getSurgeryNotes($params, $request);
       
        $loadTransformer = $this->surgeryNoteService->getSurgeryNoteTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function edit(SurgeryNote $surgeryNote)
    {
       return new SurgeryNoteResource($surgeryNote);
    }

    public function update(UpdateSurgeryNoteRequest $request, SurgeryNote $surgeryNote)
    {
        return $this->surgeryNoteService->update($request, $surgeryNote, $request->user());
    }

    public function destroy(SurgeryNote $surgeryNote)
    {
        return $surgeryNote->destroy($surgeryNote->id);
    }
}
