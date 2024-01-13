<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Http\Requests\StoreDeliveryNoteRequest;
use App\Http\Requests\UpdateDeliveryNoteRequest;
use App\Services\DeliveryNoteService;

class DeliveryNoteController extends Controller
{
    public function __construct(private readonly DeliveryNoteService $deliveryNoteService)
    {
        
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDeliveryNoteRequest $request)
    {
        return $this->deliveryNoteService->create($request, $request->user());
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DeliveryNote $deliveryNote)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDeliveryNoteRequest $request, DeliveryNote $deliveryNote)
    {
        return $this->deliveryNoteService->update($request, $deliveryNote, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeliveryNote $deliveryNote)
    {
        $deliveryNote->destroy($deliveryNote->id);
    }
}
