<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyPatientRequest;
use App\Models\Prescription;
use App\Models\Visit;
use App\Services\DatatablesService;
use App\Services\HmoService;
use Illuminate\Http\Request;

class HmoController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly HmoService $hmoService,
        private readonly SponsorCategoryController $sponsorCategoryController
        )
    {
        
    }

    public function index()
    {
        return view('hmo.hmo',
        ['categories' =>$this->sponsorCategoryController->showAll('id', 'name')]
    );
    }

    public function loadVerificationListTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->hmoService->getPaginatedVerificationList($params);
       
        $loadTransformer = $this->hmoService->getVerificationListTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadVisitsByFilterHmo(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->hmoService->getPaginatedAllConsultedHmoVisits($params, $request);
       
        $loadTransformer = $this->hmoService->getAllHmoConsultedVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function verifyPatient(VerifyPatientRequest $request, Visit $visit)
    {
        return $this->hmoService->verify($request, $visit);
    }

    public function loadHmoApprovalListTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->hmoService->getPaginatedAllPrescriptionsRequest($params, $request);
       
        $loadTransformer = $this->hmoService->getAllPrescriptionsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function approveItem(Request $request, Prescription $prescription)
    {
       return $this->hmoService->approve($request, $prescription, $request->user());
    }

    public function rejectItem(Request $request, Prescription $prescription)
    {
       return $this->hmoService->reject($request, $prescription, $request->user());
    }

    public function resetItem(Prescription $prescription)
    {
       return $this->hmoService->reset($prescription);
    }

    public function loadVisitPrescriptions(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->hmoService->getPaginatedVisitPrescriptionsRequest($params, $request);
       
        $loadTransformer = $this->hmoService->getAllPrescriptionsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function saveHmoBill(Request $request, Prescription $prescription)
    {
        return $this->hmoService->saveBill($request, $prescription, $request->user());
    }

    public function treatVisit(Request $request, Visit $visit)
    {
        return $this->hmoService->treat($visit, $request->user());
    }

    public function markBillAsSent(Request $request, Visit $visit)
    {
        return $this->hmoService->markAsSent($visit, $request->user());
    }

    public function sentBillsTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->hmoService->getSentBillsList($params, $request);
       
        $loadTransformer = $this->hmoService->getSentBillsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadReportSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        if ($request->category === 'Compare'){
            $billSummary = $this->hmoService->getReportSummaryTable($params, $request);

            return response()->json([
                'data' => $billSummary,
                'draw' => $params->draw,
                'recordsTotal' => count($billSummary),
                'recordsFiltered' => count($billSummary)
            ]);
        }
        $visits = $this->hmoService->getReportSummaryTable1($params, $request);

        $loadTransformer = $this->hmoService->getReportsSummaryTransformer($request);

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);

    }

    public function loadReconciliationTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->hmoService->getVisitsForReconciliation($params, $request);
       
        $loadTransformer = $this->hmoService->getVisitsForReconciliationTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function reconciliationPayments(Request $request, Prescription $prescription)
    {
        return $this->hmoService->savePayment($request, $prescription, $request->user());
    }

    public function reconciliationBulkPayments(Request $request, Visit $visit)
    {
        return $this->hmoService->saveBulkPayment($request, $visit);
    }

    public function loadCapitationReconciliation(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->hmoService->getNhisSponsorsByDate($params, $request);
       
        $loadTransformer = $this->hmoService->getSponsorsByDateTransformer($request);

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }
}
