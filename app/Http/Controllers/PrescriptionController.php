<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveLabResultRequest;
use App\Models\Prescription;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Services\DatatablesService;
use App\Services\PrescriptionService;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly PrescriptionService $prescriptionService
    )
    {  
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePrescriptionRequest $request)
    {
        $prescription = $this->prescriptionService->createFromDoctors($request, $request->user());

        return $prescription;
    }

    public function loadInitialTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->prescriptionService->getPaginatedInitialPrescriptions($params, $request);
       
        $loadTransformer = $this->prescriptionService->getInitialLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function loadLabTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->prescriptionService->getPaginatedLabRequests($params, $request);
       
        $loadTransformer = $this->prescriptionService->getLabTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function loadTreatmentTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->prescriptionService->getPaginatedTreatmentRequests($params, $request);
       
        $loadTransformer = $this->prescriptionService->getTreatmentTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription)
    {
        return $prescription->destroy($prescription->id);
    }
}
