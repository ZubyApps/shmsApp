@extends('layout')

@section('content')
@vite(['resources/js/investigations.js'])

@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isAnc' => false, 'isLab' => true, 'isHmo' => false, 'id' => 'treatmentDetailsModal'])
@include('nurses.treatmentDetailsModal', ['title' => 'ANC Treatment Details', 'isAnc' => true, 'isLab' => false, 'isHmo' => true, 'id' => 'ancTreatmentDetailsModal'])
@include('investigations.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])
@include('investigations.addResultModal', ['title' => 'Update Result', 'isUpdate' => true, 'id' => 'updateResultModal'])
@include('investigations.investigationsModal', ['title' => 'Investigations', 'isDoctor' => true, 'id' => 'investigationsModal'])
@include('extras.bulkRequestModal', ['title' => 'Bulk Request', 'dept' => 'Lab', 'isPharmacy' => false, 'id' => 'bulkRequestModal'])
@include('extras.labResultModal', ['title' => 'Lab Result', 'dept' => 'Lab', 'isPharmacy' => false, 'id' => 'labResultModal'])


    <div class="container p-1 mt-5 bg-white">

        <div class="offcanvas offcanvas-top overflow-auto" data-bs-scroll="true" tabindex="-1" id="offcanvasInvestigations"
            aria-labelledby="offcanvasInvestigationsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasInvestigations">Inpatient Investigations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="my-2 form-control">
                    <span class="fw-bold text-primary"> Inpatient's Investigations </span>
                    <div class="row m-1">
                        <table id="inpatientInvestigationsTable" class="table table-sm">
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

        <div class="offcanvas offcanvas-bottom overflow-auto" data-bs-scroll="true" tabindex="-1" id="offcanvasOutpatientsInvestigations"
            aria-labelledby="offcanvasOutpatientsInvestigationsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasOutpatientsInvestigations">OutPatient Investigations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="my-2 form-control">
                    <span class="fw-bold text-primary"> Outpatient's Investigations </span>
                    <div class="row overflow-auto m-1">
                        <table id="outpatientInvestigationsTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Doctor</th>
                                    <th>Patient</th>
                                    <th>Diagnosis</th>
                                    <th>Investigation</th>
                                    <th>Result</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-start mb-4">
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasInvestigations" aria-controls="offcanvasInvestigations">
                <i class="bi bi-list-check"></i>
                Inpatient's Investigation Table <span class="badge text-bg-danger" id="inpatientsInvestigationCount"></span>
            </button>
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="outpatientsInvestigationBtn" data-bs-target="#offcanvasOutpatientsInvestigations" aria-controls="offcanvasOutpatientsInvestigations">
                <i class="bi bi-list-check"></i>
                Outpatient's Investigation Table <span class="badge text-bg-danger" id="outpatientsInvestigationCount"></span>
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

                    <button class="nav-link" id="nav-bulkRequests-tab" data-bs-toggle="tab" data-bs-target="#nav-bulkRequests"
                        type="button" role="tab" aria-controls="nav-bulkRequests" aria-selected="false">Bulk Requests</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-outPatients" role="tabpanel"
                    aria-labelledby="nav-outPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="outPatientsVisitTable" class="table table-hover table-sm">
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
                                    <th>Ward</th>
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
                <!-- bulk request table -->
                <div class="tab-pane fade" id="nav-bulkRequests" role="tabpanel" aria-labelledby="nav-bulkRequests-tab" tabindex="0">
                    <div class="text-start py-4">
                        <button type="button" id="newBulkRequestBtn" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Bulk Request
                        </button>
                    </div>
                    <div class="pt-2 ">
                        <table id="bulkRequestsTable" class="table table-hover align-middle table-sm bulkRequestsTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Dept</th>
                                    <th>Request By</th>
                                    <th>Note</th>
                                    <th>Qty Dispensed</th>
                                    <th>Dispensed</th>
                                    <th>Dispensed By</th>
                                    <th>Qty Confirmed</th>
                                    <th>Confirmed By</th>
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