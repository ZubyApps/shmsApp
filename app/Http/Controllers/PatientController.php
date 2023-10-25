<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Resources\PatientResource;
use App\Models\Patient;
use App\Services\DatatablesService;
use App\Services\PatientService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(
        private readonly SponsorCategoryController $sponsorCategoryController, 
        private readonly DatatablesService $datatablesService, 
        private readonly PatientService $patientService)
    {
        
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('patients.patients', 
        ['categories' =>$this->sponsorCategoryController->showAll('id', 'name')]
    );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function sponsorCategoryOptions()
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        $patient = $this->patientService->create($request, $request->user());
        // $request->user()->patients()->create([
        //     "patient_type"          => $request->patientType,
        //     "address"               => $request->address,
        //     "blood_group"           => $request->bloodgroup,
        //     "card_no"               => $request->cardNumber,
        //     "date_of_birth"         => $request->dateOfBirth,
        //     "email"                 => $request->email,
        //     "ethnic_group"         => $request->ethnicGroup,
        //     "first_name"            => $request->firstName,
        //     "genotype"              => $request->genotype,
        //     "known_conditions"      => $request->knownConditions,
        //     "last_name"             => $request->lastName,
        //     "marital_Status"        => $request->maritalStatus,
        //     "middle_name"           => $request->middleName,
        //     "nationality"           => $request->nationality,
        //     "next_of_kin"           => $request->nextOfKin,
        //     "next_of_kin_phone"     => $request->nextOfKinPhone,
        //     "next_of_kin_rship"     => $request->nextOfKinRship,
        //     "occupation"            => $request->occupation,
        //     "phone"                 => $request->phone,
        //     "registration_bill"     => $request->registrationBill,
        //     "religion"              => $request->religion,
        //     "sex"                   => $request->sex,
        //     "sponsor_id"            => $request->sponsor,
        //     "staff_Id"              => $request->staffId,
        //     "state_of_origin"       => $request->stateOrigin,
        //     "state_of_residence"    => $request->stateResidence,
        // ]);
           
        return $patient->load('sponsor');
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->patientService->getPaginatedPatients($params);
       
        $loadTransformer = $this->patientService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        return $patient->destroy($patient->id);
    }
}
