<?php

namespace App\Http\Controllers;

use App\Http\Requests\VerifyPatientRequest;
use App\Models\Hmo;
use App\Models\Prescription;
use App\Models\Visit;
use App\Services\DatatablesService;
use App\Services\HmoService;
use Illuminate\Http\Request;

class HmoController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly HmoService $hmoService)
    {
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hmo.hmo');
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

    /**
     * HMO officers approval.
     */
    public function approveItem(Request $request, Prescription $prescription)
    {
       return $this->hmoService->approve($request, $prescription, $request->user());
    }

    public function rejectItem(Request $request, Prescription $prescription)
    {
       return $this->hmoService->reject($request, $prescription, $request->user());
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
}
