<?php

namespace App\Http\Controllers;

use App\Models\Pharmacy;
use App\Models\Prescription;
use App\Services\DatatablesService;
use App\Services\PharmacyService;
use Illuminate\Http\Request;

class PharmacyController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly PharmacyService $pharmacyService)
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
       return $this->pharmacyService->bill($request, $prescription, $request->user());
    }

    public function dispensePrescription(Request $request, Prescription $prescription)
    {
       return $this->pharmacyService->dispense($request, $prescription, $request->user());
    }

}
