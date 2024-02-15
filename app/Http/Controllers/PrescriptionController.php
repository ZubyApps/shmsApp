<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscontinuePrescriptionRequest;
use App\Http\Requests\SaveLabResultRequest;
use App\Models\Prescription;
use App\Http\Requests\StorePrescriptionRequest;
use App\Http\Requests\UpdatePrescriptionRequest;
use App\Models\Resource;
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
    public function store(StorePrescriptionRequest $request, Resource $resource)
    {
        $prescription = $this->prescriptionService->createFromDoctors($request, $resource, $request->user());

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

    public function loadMedicationTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->prescriptionService->getPaginatedMedications($params, $request);
       
        $loadTransformer = $this->prescriptionService->getPrescriptionsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function loadOtherPrescriptionsTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->prescriptionService->getOtherPrescriptions($params, $request);
       
        $loadTransformer = $this->prescriptionService->getPrescriptionsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function discontinuePrescription(DiscontinuePrescriptionRequest $request, Prescription $prescription)
    {
        return $this->prescriptionService->discontinue($request, $prescription);
    }
    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Prescription $prescription)
    {
        return $this->prescriptionService->processDeletion($prescription);
    }
}
