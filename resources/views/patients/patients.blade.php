@extends('layout')


@section('content')
@vite(['resources/js/patients.js'])

@include('patients.patientModal', ['title' => 'New Patient', 'isUpdate' => false, 'id' => 'newPatientModal'])
@include('patients.patientModal', ['title' => 'Update Patient', 'isUpdate' => true, 'id' => 'updatePatientModal'])

@include('sponsors.sponsorModal', ['title' => 'New Sponsor', 'isUpdate' => false, 'id' => 'newSponsorModal'])
@include('sponsors.sponsorModal', ['title' => 'Update Sponsor', 'isUpdate' => true, 'id' => 'updateSponsorModal'])

@include('patients.initiatePatientModal', ['title' => "Initiate Patient's Visit", 'id' => 'initiatePatientModal'])

<div class="container mt-5">
    <div class="text-start mb-4">
        <button type="button" id="newPatient" class="btn btn-primary text-white">
            <i class="bi bi-plus-circle me-1"></i>
            New Patient
        </button>
        <button type="button" id="newSponsor" class="btn btn-primary text-white mx-2">
            <i class="bi bi-plus-circle me-1"></i>
            New Sponsor
        </button>
        <button type="button" id="initiate" class="btn btn-primary text-white">
            <i class="bi bi-plus-circle me-1"></i>
            Initiate
        </button>
    </div>

    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active"  id="nav-home-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-home" type="button" role="tab" aria-controls="nav-home"
                    aria-selected="true">Patients</button>
                <button class="nav-link"  id="nav-profile-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-profile" type="button" role="tab" aria-controls="nav-profile"
                    aria-selected="false">Sponsors</button>
                <button class="nav-link"   id="nav-contact-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-contact" type="button" role="tab" aria-controls="nav-contact"
                    aria-selected="false">Active</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- patients table -->
            <div class="tab-pane fade show active" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab"
                tabindex="0">
                <div class="py-4">
                    <table id="allPatientsTable"
                        class="table table-hover align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Sex</th>
                                <th>DOB</th>
                                <th>Sponsor Cat</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder text-primary">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- sponsors table -->
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab" tabindex="0">
                <div class="py-4 ">
                    <table id="sponsorsTable"
                        class="table table-hover align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Sponsor Name</th>
                                <th>Payment Category</th>
                                <th>Sponsor Category</th>
                                <th>Payment Matrix</th>
                                <th>Balance Required?</th>
                                <th>Registration Bill</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder text-primary">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <!-- active table -->
            <div class="tab-pane fade" id="nav-contact" role="tabpanel" aria-labelledby="nav-contact-tab" tabindex="0">
                <div class="py-4">
                    <table  id="ActivePatientsTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Sex</th>
                                <th>DOB</th>
                                <th>Sponsor Cat</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot class="fw-bolder text-primary">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
</div>


@endsection