<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCapitationPaymentRequest;
use App\Models\CapitationPayment;
use App\Services\CapitationPaymentService;
use App\Services\DatatablesService;
use Illuminate\Http\Request;

class CapitationPaymentController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly CapitationPaymentService $capitationPaymentService)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCapitationPaymentRequest $request)
    {
        return $this->capitationPaymentService->create($request, $request->user());
    }

    public function loadCapitationPayments(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->capitationPaymentService->getCapitationPayments($params, $request);
       
        $loadTransformer = $this->capitationPaymentService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CapitationPayment $capitationPayment)
    {
        return $this->capitationPaymentService->processDeletion($capitationPayment);
    }
}
