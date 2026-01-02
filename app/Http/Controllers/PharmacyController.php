<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\Visit;
use App\Services\DatatablesService;
use App\Services\PharmacyService;
use App\Services\PrescriptionService;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly PharmacyService $pharmacyService,
        private readonly PrescriptionService $prescriptionService)
    {
        
    }

    public function index()
    {
        return view('pharmacy.pharmacy');
    }

    public function loadVisitsByFilterPharmacy(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->pharmacyService->getpaginatedFilteredPharmacyVisits($params, $request);
       
        $loadTransformer = $this->pharmacyService->getPharmacyVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $visits, $params);  
    }

    public function loadConsultationPrescriptions(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $consultations = $this->pharmacyService->getPrescriptionsByConsultation($params, $request);
       
        $loadTransformer = $this->pharmacyService->getprescriptionByConsultationTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $consultations, $params);  
    }

    public function billPrescription(Request $request, Prescription $prescription)
    {
        if ($request->notPharmacy){
           return $this->pharmacyService->bill($request, $prescription, $request->user());
        }
        
        $request->validate([
            'quantity' => ['nullable', 'integer', 'lte:'.$prescription->resource->stock_level]
        ]);
        
       return $this->pharmacyService->bill($request, $prescription, $request->user());
    }

    public function dispensePrescription(Request $request, Prescription $prescription)
    {
        $request->validate([
                'quantity' => ['nullable', 'integer', 'lte:'.$prescription->resource->stock_level]
        ]);
        

       return $this->pharmacyService->dispense($request, $prescription, $request->user());
    }

    public function holdPrescription(Request $request, Prescription $prescription)
    {
        $request->validate([
                'reason' => ['nullable', 'string']
        ]);
        

       return $this->prescriptionService->hold($request, $prescription, $request->user());
    }

    public function dispenseComment(Request $request, Prescription $prescription)
    {
        return $this->pharmacyService->saveDispenseComment($request, $prescription);
    }

    public function expirationStock(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $expirationStock = $this->pharmacyService->getExpirationStock($params, $request);
       
        $transformer = $this->pharmacyService->getExpirationStockTransformer();

        return $this->datatablesService->datatableResponse($transformer, $expirationStock, $params);  
    }

    public function pharmacyDone(Request $request, Visit $visit)
    {
        return $this->pharmacyService->done($visit, $request->user());
    }
}
