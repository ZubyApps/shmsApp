<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\DatatablesService;
use App\Services\PatientService;
use App\Services\VisitService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(
        private readonly SponsorCategoryController $sponsorCategoryController, 
        private readonly DatatablesService $datatablesService, 
        private readonly PatientService $patientService,
        private readonly VisitService $visitService
        )
    {
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // dd($this->visitService->getVisitSummaryBySponsor('name'));
        return view('patients.patients', 
        ['categories' => $this->sponsorCategoryController->showAll('id', 'name')]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        return $this->patientService->create($request, $request->user());        
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->patientService->getPaginatedPatients($params);
       
        $loadTransformer = $this->patientService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Patient $patient)
    {
        return new PatientResource($patient);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        return $this->patientService->update($request, $patient, $request->user());
    
    }

    public function updateKnownclinicalInfo(Request $request, Patient $patient)
    {
        $patientResponse = $this->patientService->updateKnownClinicalInfo($request, $patient, $request->user());

        return $patientResponse->only(
            [
                'id',
                'blood_group' , 
                'genotype', 
                'known_conditions']);
    }

    public function loadRegSummaryBySponsor(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->patientService->getNewRegSummaryBySponsor($params, $request);

        return response()->json([
            'data' => $sponsors,
            'draw' => $params->draw,
            'recordsTotal' => $sponsors->total(),
            'recordsFiltered' => $sponsors->total()
        ]);
    }

    public function loadSummaryBySex(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sexes = $this->patientService->getSummaryBySex($params, $request);

        return response()->json([
            'data' => $sexes,
            'draw' => $params->draw,
            'recordsTotal' => count($sexes),
            'recordsFiltered' => count($sexes)
        ]);
    }

    public function loadSummaryByAge(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $ages = $this->patientService->getSummaryByAge($params, $request);

        return response()->json([
            'data' => $ages,
            'draw' => $params->draw,
            'recordsTotal' => count($ages),
            'recordsFiltered' => count($ages)
        ]);
    }

    public function loadVisitSummaryBySponsor(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->visitService->getVisitSummaryBySponsor($params, $request);
        
        return response()->json([
            'data' => $visits,
            'draw' => $params->draw,
            'recordsTotal' => $visits->total(),
            'recordsFiltered' => $visits->total()
        ]);
    }
    
    public function loadPatientsBySponsor(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->patientService->getPatientsBySponsor($params, $request);

        $loadTransformer = $this->patientService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadVisit(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->visitService->getVisits($params, $request);

        $loadTransformer = $this->visitService->getVisitsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        return $patient->destroy($patient->id);
    }
}
