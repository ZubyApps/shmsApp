<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateCommunicationRequest;
use App\Models\Communication;
use App\Services\CommunicationService;
use App\Services\DatatablesService;
use App\Services\SmsWalletService;
use Illuminate\Http\Request;

class CommunicationController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly CommunicationService $communicationService,
        )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('communicationservices.communicationServices', []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function getCurrentBalance()
    {
        return SmsWalletService::currentBalance();
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $smses = $this->communicationService->getPaginatedSmses($params);
       
        $loadTransformer = $this->communicationService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $smses, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Communication $communication)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommunicationRequest $request, Communication $communication)
    {
        //
    }

    public function destroy(Communication $communication)
    {
        return $communication->destroy($communication->id);
    }
}
