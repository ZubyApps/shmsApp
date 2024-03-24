@extends('layout')

@section('content')
@vite(['resources/js/thirdPartyServices.js'])

@include('thirdpartyservices.thirdPartyModal', ['title' => 'New Third Party', 'isUpdate' => false, 'id' => 'newthirdPartyModal'])
@include('thirdpartyservices.thirdPartyModal', ['title' => 'New Third Party', 'isUpdate' => true, 'id' => 'updatethirdPartyModal'])


    <div class="container p-1 mt-5 bg-white">
        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-listOfServices-tab" data-bs-toggle="tab" data-bs-target="#nav-listOfServices" 
                    type="button" role="tab" aria-controls="nav-listOfServices" aria-selected="true">List of Services</button>

                    <button class="nav-link" id="nav-thirdParties-tab" data-bs-toggle="tab" data-bs-target="#nav-thirdParties"
                        type="button" role="tab" aria-controls="nav-thirdParties" aria-selected="false">Third Parties</button>

                    {{-- <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients"
                        type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>

                    <button class="nav-link" id="nav-bulkRequests-tab" data-bs-toggle="tab" data-bs-target="#nav-bulkRequests"
                        type="button" role="tab" aria-controls="nav-bulkRequests" aria-selected="false">Bulk Requests</button> --}}
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-listOfServices" role="tabpanel"
                    aria-labelledby="nav-listOfServices-tab" tabindex="0">
                    <div class="py-5">
                        <table id="listOfServicesTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Thrid Party</th>
                                    <th>Service</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Status</th>
                                    <th>HMS Bill</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- active table -->
                <div class="tab-pane fade" id="nav-thirdParties" role="tabpanel" aria-labelledby="nav-thirdParties-tab" tabindex="0">
                    <div class="py-4">
                        <div class="text-start py-3">
                            <button type="button" id="newThirdPartyBtn" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Third Party
                            </button>
                        </div>
                        <table id="thirdPartiesTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Short Name</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                    <th>Comment</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
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
                                    <th>Qty Approved</th>
                                    <th>Approved By</th>
                                    <th>Qty Dispensed</th>
                                    <th>Dispensed</th>
                                    <th>Dispensed By</th>
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