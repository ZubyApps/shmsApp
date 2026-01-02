<?php

namespace App\Http\Controllers;

use App\Http\Requests\DiscontinuePrescriptionRequest;
use App\Models\Prescription;
use App\Http\Requests\StorePrescriptionRequest;
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

    public function store(StorePrescriptionRequest $request, Resource $resource)
    {
        $prescription = $this->prescriptionService->createPrescription($request, $resource, $request->user());

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

    public function loadEmergencyTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->prescriptionService->getEmergencyPrescriptions($params, $request);
       
        $loadTransformer = $this->prescriptionService->getEmergencyPrescriptionsformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function confirmPrescription(Prescription $prescription)
    {
        return $this->prescriptionService->confirm($prescription);
    }

    public function discontinuePrescription(DiscontinuePrescriptionRequest $request, Prescription $prescription)
    {
        return $this->prescriptionService->discontinue($request, $prescription);
    }
    
    public function destroy(Prescription $prescription)
    {
        return $this->prescriptionService->processDeletion($prescription);
    }
}
