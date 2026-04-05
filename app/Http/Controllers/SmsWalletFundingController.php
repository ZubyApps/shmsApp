<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSmsWalletFundingRequest;
use App\Http\Requests\UpdateSmsWalletFundingRequest;
use App\Models\SmsWalletFunding;
use App\Services\DatatablesService;
use App\Services\SmsWalletFundingService;
use Illuminate\Http\Request;

class SmsWalletFundingController extends Controller
{

    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly SmsWalletFundingService $smsWalletFundingService,
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
    public function store(StoreSmsWalletFundingRequest $request)
    {
        return $this->smsWalletFundingService->create($request, $request->user());
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->smsWalletFundingService->getPaginatedSmsWalletFundings($params);
       
        $loadTransformer = $this->smsWalletFundingService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }

    /**
     * Update the specified resource in storage.
     */
    public function updatePayment(UpdateSmsWalletFundingRequest $request, SmsWalletFunding $smsWalletFunding)
    {
        return $this->smsWalletFundingService->updatePaymentStatus($request, $smsWalletFunding, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SmsWalletFunding $smsWalletFunding)
    {
        return $smsWalletFunding->destroy($smsWalletFunding->id);
    }
}
