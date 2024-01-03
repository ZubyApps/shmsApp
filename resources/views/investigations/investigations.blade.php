@extends('layout')

@section('content')
@vite(['resources/js/investigations.js'])

@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isLab' => true, 'isHmo' => false, 'id' => 'treatmentDetailsModal'])
@include('investigations.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])
@include('investigations.investigationsModal', ['title' => 'Investigations', 'isDoctor' => true, 'id' => 'investigationsModal'])


    <div class="container p-1 mt-5 bg-white">

        <div class="offcanvas offcanvas-top overflow-auto" data-bs-scroll="true" tabindex="-1" id="offcanvasInvestigations"
            aria-labelledby="offcanvasInvestigationsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasInvestigations">Investigation Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="my-2 form-control">
                    <span class="fw-bold text-primary"> Inpatient's Investigations </span>
                    <div class="row m-1">
                        <table id="inpatientInvestigationsTable" class="table table-hover align-middle table-sm bg-primary">
                            <thead>
                                <tr>
                                    <th>Requested</th>
                                    <th>Type</th>
                                    <th>Doctor</th>
                                    <th>Patient</th>
                                    <th>Diagnosis</th>
                                    <th>Investigation</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-start mb-4">
            {{-- <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
                <i class="bi bi-list-check"></i>
                Waiting List
            </button> --}}
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasInvestigations" aria-controls="offcanvasInvestigations">
                <i class="bi bi-list-check"></i>
                Inpatient's Investigation Table
            </button>
        </div>

        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-outPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-outPatients" 
                    type="button" role="tab" aria-controls="nav-outPatients" aria-selected="true">OutPatients</button>

                    <button class="nav-link" id="nav-inPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-inPatients"
                        type="button" role="tab" aria-controls="nav-inPatients" aria-selected="false">Inpatients</button>

                    <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients"
                        type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-outPatients" role="tabpanel"
                    aria-labelledby="nav-outPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="outPatientsVisitTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Investigations</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- active table -->
                <div class="tab-pane fade" id="nav-inPatients" role="tabpanel" aria-labelledby="nav-inPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="inPatientsVisitTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Investigations</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- sponsors table -->
                <div class="tab-pane fade" id="nav-ancPatients" role="tabpanel" aria-labelledby="nav-ancPatients-tab" tabindex="0">
                    <div class="py-4 ">
                        <table id="ancPatientsVisitTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Investigations</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection