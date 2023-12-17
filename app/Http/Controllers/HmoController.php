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
    public function store(Request $request)
    {
        //
    }

    public function loadVerificationListTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->hmoService->getPaginatedVerificationList($params);
       
        $loadTransformer = $this->hmoService->getVerificationListTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function verifyPatient(VerifyPatientRequest $request, Visit $visit)
    {
        if ($request->status == 'Verified'){
            return $visit->update([
                'verification_status'   => true,
                'verification_code'     => $request->codeText
            ]);
        }

        return;
    }

    public function loadHmoApprovalTable(Request $request)
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
}
