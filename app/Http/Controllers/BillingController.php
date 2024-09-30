<?php

namespace App\Http\Controllers;

use App\Http\Requests\DischargeBillRequest;
use App\Http\Requests\DiscountRequest;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Models\Visit;
use App\Services\BillingService;
use App\Services\DatatablesService;
use App\Services\ExpenseService;
use App\Services\PaymentService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly BillingService $billingService,
        private readonly PaymentService $paymentService,
        private readonly ExpenseService $expenseService,
        private readonly ExpenseCategoryController $expenseCategoryController,
        private readonly UserService $userService,
        private readonly ThirdPartyController $thirdPartyController,
        private readonly MarkedForController $markedForController,
        )
    {
        
    }

    public function index()
    {
        return view('billing.billing', [
            'users'         => $this->userService->listStaff(special_note:'Management'),
            'categories'    => $this->expenseCategoryController->showAll('id', 'name'),
            'thirdParties'  => $this->thirdPartyController->showAll('id', 'short_name'),
            'markedFors'    => $this->markedForController->showAll('id', 'name'),
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        $payment = $this->paymentService->create($request, $request->user());
        
        return $payment->load('user');
    }

    public function loadVisitsByFilterBilling(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->billingService->getpaginatedFilteredBillingVisits($params, $request);
       
        $loadTransformer = $this->billingService->getVisitsBillingTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadPatientBill(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $bills = $this->billingService->getPatientBillTable($params, $request);
       
        $loadTransformer = $this->billingService->getPatientBillTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $bills, $params);  
    }

    public function loadBillSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $billSummary = $this->billingService->getPatientBillSummaryTable($request);

        return response()->json([
            'data' => $billSummary,
            'draw' => $params->draw,
            'recordsTotal' => count($billSummary),
            'recordsFiltered' => count($billSummary)
        ]);
    }

    public function loadPatientPayment(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->billingService->getPatientPaymentTable($params, $request);
       
        $loadTransformer = $this->billingService->getPatientPaymentTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function saveDiscount(DiscountRequest $request, Visit $visit)
    {
        return $this->billingService->saveDiscount($request, $visit, $request->user());
    }

    public function destroy(Request $request, Payment $payment)
    {
        if ($request->user()->designation?->access_level < 4) {
            return response()->json(['message' => 'You are not authorized'], 403);
        }
        return $this->billingService->processPaymentDestroy($payment);
    }

    public function loadVisitsWithOutstandingBills(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->billingService->getVisitsWithOutstandingBills($params, $request);
       
        $loadTransformer = $this->billingService->getVisitsBillingTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadAllOpenVisits(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->billingService->getpaginatedFilteredBillingVisits($params, $request);
       
        $loadTransformer = $this->billingService->getVisitsBillingTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadExpenses(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->expenseService->getPaginatedExpenses($params, $request);
       
        $loadTransformer = $this->expenseService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadCashPaymentsAndExpenses(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $payment = $this->paymentService->getCashPaymentsByDate($request);
        $expense = $this->expenseService->getExpensesByDate($request);

        $combined[] = [
            'date' => $request->date ? (new Carbon($request->date))->format('d/m/Y') : (new Carbon())->format('d/m/Y'),
            'id' => $payment?->id,
            'totalCash' => $payment?->totalCash,
            'totalExpense' => $expense?->totalExpense
        ];

        return response()->json([
            'data' => $combined,
            'draw' => $params->draw,
            'recordsTotal' => count($combined),
            'recordsFiltered' => count($combined)
        ]);

    }

    public function addDischargeBill(DischargeBillRequest $request)
    {
        return $this->billingService->saveDischargeBill($request, $request->user());
    }
}
