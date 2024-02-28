@extends('layout')


@section('content')
@vite(['resources/js/users.js'])

@include('auth.newstaffModal', ['title' => 'Register Staff', 'isUpdate' => false, 'id' => 'newStaffModal'])
@include('auth.newstaffModal', ['title' => 'Edit Staff', 'isUpdate' => true, 'id' => 'editStaffModal'])
@include('auth.designationModal', ['title' => 'Assign Designation', 'id' => 'designationModal'])

<div class="container mt-5 bg-white">
    <div class="container p-1 mt-5 bg-white">
        <div class="offcanvas offcanvas-start overflow-auto" data-bs-scroll="true" tabindex="-1" id="activeListOffcanvas2"
        aria-labelledby="activeListOffcanvasLabel" aria-expanded="false">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary" id="activeListOffcanvasLabel">List of currently logged in staff</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="py-4 ">
                <table id="activeStaffTable" class="table table-sm">
                    <thead>
                        <tr>
                            <th>Logged in</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- <div class="offcanvas offcanvas-top" data-bs-scroll="true" tabindex="-1" id="offcanvasInvestigations"
        aria-labelledby="offcanvasInvestigationsLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary" id="offcanvasInvestigations">Investigations</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas2-body">
            <div class="my-2 form-control">
                <span class="fw-bold text-primary"> Outpatient's Investigations </span>
                <div class="row overflow-auto m-1">
                    <table id="outpatientInvestigationsTable" class="table table-hover table-sm">
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
    </div> --}}

    <div class="text-start mb-4">
        <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="activeUsersBtn" data-bs-target="#activeListOffcanvas2" aria-controls="activeListOffcanvas2">
            <i class="bi bi-list-check"></i>
            Active Staff
        </button>
    </div>

    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-users-tab" data-bs-toggle="tab" data-bs-target="#nav-users" 
                    type="button" role="tab" aria-controls="nav-outPatients" aria-selected="true">All Staff</button>

                <button class="nav-link" id="nav-inPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-inPatients"
                    type="button" role="tab" aria-controls="nav-inPatients" aria-selected="false">Staff Aggregation</button>

                {{-- <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients"
                    type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button> --}}
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- patients table -->
            <div class="tab-pane fade show active" id="nav-outPatients" role="tabpanel"
                aria-labelledby="nav-outPatients-tab" tabindex="0">
                <div class="text-start py-3">
                    <button type="button" id="newStaffBtn" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Staff
                    </button>
                </div>
                <div class="py-4">
                    <table id="allStaffTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Employed</th>
                                <th>Designation</th>
                                <th>Last Login</th>
                                <th>Last Logout</th>
                                <th>Qualification</th>
                                <th>Username</th>
                                <th>Phone</th>
                                {{-- <th>Address</th> --}}
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- inpatients table -->
            <div class="tab-pane fade" id="nav-inPatients" role="tabpanel" aria-labelledby="nav-inPatients-tab"
                tabindex="0">
                <div class="py-4 ">
                    <table id="inPatientsVisitTable" class="table table-hover align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Designation</th>
                                <th>Number of Staff</th>
                                <th>Doctor</th>
                                <th>Current Diagnosis</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- Anc table -->
            {{-- <div class="tab-pane fade" id="nav-ancPatients" role="tabpanel" aria-labelledby="nav-ancPatients-tab"
                tabindex="0">
                <div class="py-4 ">
                    <table id="ancPatientsVisitTable" class="table table-hover align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Seen</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Current Diagnosis</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div> --}}
        </div>
    </div>
</div>

@endsection