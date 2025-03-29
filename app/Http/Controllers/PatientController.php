<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateFormRequest;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientPreFormResource;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Models\PatientPreForm;
use App\Services\DatatablesService;
use App\Services\PatientService;
use App\Services\PrePatientService;
use App\Services\UserService;
use App\Services\VisitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PatientController extends Controller
{
    public function __construct(
        private readonly SponsorCategoryController $sponsorCategoryController, 
        private readonly DatatablesService $datatablesService, 
        private readonly PatientService $patientService,
        private readonly VisitService $visitService,
        private readonly UserService $userService,
        private readonly PrePatientService $prePatientService
        )
    {
        
    }

    public function index()
    {
        return view('patients.patients', [
            'categories' => $this->sponsorCategoryController->showAll('id', 'name'),
            'doctors' => $this->userService->listStaff(designation: 'Doctor'),
            'feverBenchMark' => Cache::get('feverBenchmark', 37.3)
            ]
        );
    }

    public function store(StorePatientRequest $request)
    {
        return $this->patientService->create($request, $request->user());        
    }

    public function generateLink(GenerateFormRequest $request)
    {
        return $this->patientService->sendFormLink($request, $request->user());
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $patients = $this->patientService->getPaginatedPatients($params);
       
        $loadTransformer = $this->patientService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);  
    }

    public function loadPrePatients(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $prePatients = $this->prePatientService->getPaginatedPrePatients($params);
       
        $loadTransformer = $this->prePatientService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $prePatients, $params);  
    }

    public function edit(Patient $patient)
    {
        return new PatientResource($patient);
    }

    public function review(PatientPreForm $patientPreForm)
    {
        return new PatientPreFormResource($patientPreForm);
    }

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

    public function destroy(Patient $patient)
    {
        return $patient->destroy($patient->id);
    }

    public function destroyPrepatient(PatientPreForm $patientPreForm)
    {
        return $this->patientService->deletePrePatient($patientPreForm->id);
    }

    public function listPatients(Request $request)
    {
        $patients = $this->patientService->patientList($request);

        $listTransformer = $this->patientService->listTransformer();

        return array_map($listTransformer, (array)$patients->getIterator());

    }
}
