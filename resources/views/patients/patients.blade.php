@extends('layout')


@section('content')
@vite(['resources/css/colourblink.scss', 'resources/js/patients.js'])

@include('patients.patientModal', ['title' => 'New Patient', 'isUpdate' => false, 'isConfirm' => false, 'id' => 'newPatientModal'])
@include('patients.patientModal', ['title' => 'Update Patient', 'isUpdate' => true, 'isConfirm' => false, 'id' => 'updatePatientModal'])
@include('patients.patientModal', ['title' => 'Confirm Patient', 'isUpdate' => true, 'isConfirm' => true, 'id' => 'confirmPatientModal'])

@include('sponsors.sponsorModal', ['title' => 'New Sponsor', 'isUpdate' => false, 'id' => 'newSponsorModal'])
@include('sponsors.sponsorModal', ['title' => 'Update Sponsor', 'isUpdate' => true, 'id' => 'updateSponsorModal'])

@include('patients.initiatePatientModal', ['title' => "Initiate Patient's Visit", 'id' => 'initiatePatientModal'])
@include('patients.patientsBySponsorModal', ['title' => 'Patients', 'id' => 'patientsBySponsorModal'])
@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isAnc' => false, 'isLab' => false, 'isHmo' => true, 'id' => 'treatmentDetailsModal'])
@include('nurses.treatmentDetailsModal', ['title' => 'ANC Treatment Details', 'isAnc' => true, 'isLab' => false, 'isHmo' => true, 'id' => 'ancTreatmentDetailsModal'])
@include('doctors.appointmentModal', ['title' => 'Set Appointment', 'isDoctor' => false, 'id' => 'appointmentModal'])

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
                <button class="nav-link"  id="nav-visits-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-visits" type="button" role="tab" aria-controls="nav-visits"
                    aria-selected="false">Visits</button>
                <button class="nav-link"   id="nav-summaries-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-summaries" type="button" role="tab" aria-controls="nav-summaries"
                    aria-selected="false">Summaries</button>
                <button class="nav-link"   id="nav-prePatients-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-prePatients" type="button" role="tab" aria-controls="nav-prePatients"
                    aria-selected="false">Pre Patients</button>
                <button class="nav-link"   id="nav-appointments-tab" data-bs-toggle="tab"
                    data-bs-target="#nav-appointments" type="button" role="tab" aria-controls="nav-appointments"
                    aria-selected="false">Appointments</button>
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
                    <table id="allPatientsTable" class="table table-hover table-sm">
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
                                <th>Max Pay Days</th>
                                <th>Flag</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- Visit Details table -->
            <div class="tab-pane fade" id="nav-visits" role="tabpanel" aria-labelledby="nav-visits-tab" tabindex="0">
                <x-form-div class="col-xl-8 pt-3 datesDiv">
                    <x-input-span id="filterListLabel">Filter List<x-required-span /></x-input-span>
                    <select class="form-select form-select-md" name="filterListBy" id="filterListBy">
                        <option value="">All</option>
                        <option value="Outpatient">Outpatients</option>
                        <option value="Inpatient">Inpatients</option>
                        <option value="Observation">Observation</option>
                        <option value="ANC">ANC</option>
                    </select>
                    <x-input-span class="">Start</x-input-span>
                    <x-form-input type="date" name="startDate" id="startDate" />
                    <x-input-span class="">End</x-input-span>
                    <x-form-input type="date" name="endDate" id="endDate" />
                    <button class="input-group-text searchVisitsWithDatesBtn">Search</button>
                </x-form-div>
                <div class="py-2 ">
                    <table id="visitsTable" class="table">
                        <thead>
                            <tr>
                                <th>Came</th>
                                <th>Seen</th>
                                <th>Type</th>
                                <th>Patient</th>
                                <th><i class="bi bi-telephone-outbound-fill text-primary"></th>
                                <th>Address</th>
                                <th>State</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>N.O.K</th>
                                <th>N.O.K <i class="bi bi-telephone-outbound-fill text-primary"></th>
                                <th>Status</th>
                                <th>Sponsor</th>
                                <th>Category</th>
                                <th>Doctor</th>
                                <th>Diagnosis</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            <!-- summaries tables -->
            <div class="tab-pane fade" id="nav-summaries" role="tabpanel" aria-labelledby="nav-summaries-tab" tabindex="0">
                <div class="py-4">
                    <div class="row">
                        <div class="col-xl-6 mb-2">
                          <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">All Patients by Sex</h5>
                                <table  id="sexAggregateTable" class="table table-sm">
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
                                <h5 class="card-title">All Patients by Age and Sex</h5>
                                <table  id="ageAggregateTable" class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Sex</th>
                                            <th>0-3m</th>
                                            <th>3-12m</th>
                                            <th>1-5y</th>
                                            <th>5-13y</th>
                                            <th>13-18y</th>
                                            <th>18-48y</th>
                                            <th>48-63y</th>
                                            <th>63</th>
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
                                <h5 class="card-title">New Registrations by Sponsor This Month</h5>
                                <x-form-div class="col-xl-8 py-3 newRegisterationsDiv">
                                    <x-input-span class="">Month/Year</x-input-span>
                                    <x-form-input type="month" name="regMonth" id="regMonth" />
                                    <button class="input-group-text searchNewRegPatientsByMonthBtn">Search</button>
                                </x-form-div>
                                <table  id="newRegPatientsTable" class="table table-sm">
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
                            <h5 class="card-title">Visits Summary by Sponsor This Month</h5>
                            <x-form-div class="col-xl-8 py-3 visistSummaryDiv">
                                <x-input-span class="">Month/Year</x-input-span>
                                <x-form-input type="month" name="visitSummaryMonth" id="visitSummaryMonth" />
                                <button class="input-group-text searchVisitsByMonthBtn">Search</button>
                            </x-form-div>
                            <table  id="visitsSummaryTable" class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Sponsor</th>
                                        <th>Outpatients</th>
                                        <th>Inpatients</th>
                                        <th>Observations</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                                <tfoot class="fw-bolder text-primary">
                                    <tr>
                                        <td class="text-center">Total</td>
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
            </div>
            <!-- patients form table -->
            <div class="tab-pane fade" id="nav-prePatients" role="tabpanel" aria-labelledby="nav-prePatients-tab" tabindex="0">
                {{-- <div class="text-start py-3">
                    <button type="button" id="newPatient" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Patient
                    </button>
                </div> --}}
                <div class="py-2">
                    <table id="prePatientsTable" class="table table-striped table-sm">
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
            <!-- appointments table -->
            <div class="tab-pane fade" id="nav-appointments" role="tabpanel" aria-labelledby="nav-appointments-tab" tabindex="0">
                {{-- <div class="text-start py-3">
                    <button type="button" id="newPatient" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Patient
                    </button>
                </div> --}}
                <div class="py-2">
                    <table id="appointmentsTable" class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Created</th>
                                <th>Patient</th>
                                <th><i class="bi bi-telephone-outbound-fill text-primary"></th>
                                <th>Sponsor</th>
                                <th>Last Visit</th>
                                <th>Last Diagnosis</th>
                                <th>Doctor</th>
                                <th>Date</th>
                                <th>CreatedBy</th>
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