<?php

namespace App\Http\Controllers;

use App\Models\NursesNote;
use App\Http\Requests\StoreNursesNoteRequest;
use App\Http\Requests\UpdateNursesNoteRequest;
use App\Services\NursesNoteService;

class NursesNoteController extends Controller
{
    public function __construct(private readonly NursesNoteService $nursesNoteService)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNursesNoteRequest $request)
    {
        return $this->nursesNoteService->create($request, $request->user());
    }

    /**
     * Display the specified resource.
     */
    public function show(NursesNote $nursesNote)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NursesNote $nursesNote)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNursesNoteRequest $request, NursesNote $nursesNote)
    {
        return $this->nursesNoteService->update($request, $nursesNote, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NursesNote $nursesNote)
    {
        $nursesNote->destroy($nursesNote->id);
    }
}
