<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaymentRequest;
use App\Services\BillingService;
use App\Services\DatatablesService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly BillingService $billingService,
        private readonly PaymentService $paymentService)
    {
        
    }

    public function index()
    {
        return view('billing.billing');
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
    public function store(StorePaymentRequest $request)
    {
        $payment = $this->paymentService->create($request, $request->user());
        
        return $payment->load('user');
    }

    public function loadVisitsByFilterBilling(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->billingService->getpaginatedFilteredNurseVisits($params, $request);
       
        $loadTransformer = $this->billingService->getConsultedVisitsNursesTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }
}
