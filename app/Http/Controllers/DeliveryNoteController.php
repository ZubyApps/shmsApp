<?php

namespace App\Http\Controllers;

use App\Models\DeliveryNote;
use App\Http\Requests\StoreDeliveryNoteRequest;
use App\Http\Requests\UpdateDeliveryNoteRequest;
use App\Http\Resources\DeliveryNoteResource;
use App\Services\DatatablesService;
use App\Services\DeliveryNoteService;
use Illuminate\Http\Request;

class DeliveryNoteController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly DeliveryNoteService $deliveryNoteService)
    {
        
    }

    public function store(StoreDeliveryNoteRequest $request)
    {
        return $this->deliveryNoteService->create($request, $request->user());
    }

    public function loadDeliveryNoteTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->deliveryNoteService->getDeliveryNotes($params, $request);
       
        $loadTransformer = $this->deliveryNoteService->getDeliveryNoteTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function edit(DeliveryNote $deliveryNote)
    {
        return new DeliveryNoteResource($deliveryNote);
    }

    public function update(UpdateDeliveryNoteRequest $request, DeliveryNote $deliveryNote)
    {
        return $this->deliveryNoteService->update($request, $deliveryNote, $request->user());
    }

    public function destroy(DeliveryNote $deliveryNote)
    {
        $deliveryNote->destroy($deliveryNote->id);
    }
}
