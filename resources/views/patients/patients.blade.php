@extends('layout')


@section('content')
@vite(['resources/js/patients.js'])

@include('patients.patientModal', ['title' => 'New Patient', 'isUpdate' => false, 'id' => 'newPatientModal'])
@include('patients.patientModal', ['title' => 'Update Patient', 'isUpdate' => true, 'id' => 'updatePatientModal'])

@include('sponsors.sponsorModal', ['title' => 'New Sponsor', 'isUpdate' => false, 'id' => 'newSponsorModal'])
@include('sponsors.sponsorModal', ['title' => 'Update Sponsor', 'isUpdate' => true, 'id' => 'updateSponsorModal'])

@include('patients.initiatePatientModal', ['title' => "Initiate Patient's Visit", 'id' => 'initiatePatientModal'])

<div class="container mt-5">
    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active"  id="nav-patients-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-patients" type="button" role="tab" aria-controls="nav-patients"
                    aria-selected="true">Patients</button>
                <button class="nav-link"  id="nav-sponsors-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-sponsors" type="button" role="tab" aria-controls="nav-sponsors"
                    aria-selected="false">Sponsors</button>
                <button class="nav-link"   id="nav-summaries-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-summaries" type="button" role="tab" aria-controls="nav-summaries"
                    aria-selected="false">Summaries</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- patients table -->
            <div class="tab-pane fade show active" id="nav-patients" role="tabpanel" aria-labelledby="nav-patients-tab" tabindex="0">
                <div class="text-start py-3">
                    <button type="button" id="newPatient" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Patient
                    </button>
                </div>
                <div class="py-2">
                    <table id="allPatientsTable" class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Card</th>
                                <th>Patient Name</th>
                                <th><i class="bi bi-telephone-outbound-fill text-primary"></th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Sponsor</th>
                                <th>Category</th>
                                <th>Created</th>
                                <th>CreatedBy</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- sponsors table -->
            <div class="tab-pane fade" id="nav-sponsors" role="tabpanel" aria-labelledby="nav-sponsors-tab" tabindex="0">
                <div class="text-start py-3">
                    <button type="button" id="newSponsor" class="btn btn-primary text-white">
                        <i class="bi bi-plus-circle me-1"></i>
                        Sponsor
                    </button>
                </div>
                <div class="py-2 ">
                    <table id="sponsorsTable" class="table table-striped table-sm">
                        <thead>
                            <tr>
                                <th>Sponsor Name</th>
                                <th> <i class="bi bi-telephone-outbound-fill text-primary"></i></th>
                                <th> <i class="bi bi-envelope-at-fill text-primary"></i> </th>
                                <th>Category</th>
                                <th>Approval</th>
                                <th>Registration Bill</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- active table -->
            <div class="tab-pane fade" id="nav-summaries" role="tabpanel" aria-labelledby="nav-summaries-tab" tabindex="0">
                <div class="py-4">
                    <div class="row">
                        <div class="col-xl-6 mb-2">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">All Patients by Sex</h5>
                                <table  id="sexAggregateTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sex</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td class="text-center">Total</td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-xl-6">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">All Patients by Age</h5>
                                <table  id="ageAggregateTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sex</th>
                                            <th>Under 5</th>
                                            <th>5-12</th>
                                            <th>12-18</th>
                                            <th>18-50</th>
                                            <th>Above 50</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td class="text-center">Totals</td>
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
                <div class="py-4">
                    <div class="row">
                        <div class="col-xl-6 mb-2">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">New Registerations by Sponsor</h5>
                                <table  id="totalPatientsTable" class="table table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sponsor</th>
                                            <th>Count</th>
                                            <th>Category</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot class="fw-bolder text-primary">
                                        <tr>
                                            <td class="text-center">Total</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                          </div>
                        </div>
                        <div class="col-xl-6">
                          <div class="card">
                            <div class="card-body">
                            <h5 class="card-title">New Registerations by Sponsor</h5>
                            <table  id="totalPatientsTable" class="table table-hover table-sm">
                                <thead>
                                    <tr>
                                        <th>Sponsor</th>
                                        <th>Count</th>
                                        <th>Category</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="fw-bolder text-primary">
                                    <tr>
                                        <td class="text-center">Total</td>
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
            </div>
        </div>
    </div>
    
</div>


@endsection