@extends('layout')

@section('content')
@vite(['resources/js/adminSettings.js'])

{{-- @include('hmo.verifyModal', ['title' => 'Verify Patient', 'isUpdate' => false, 'id' => 'verifyModal'])
@include('hmo.treatmentDetailsModal', ['title' => 'Treatment Details', 'isUpdate' => false, 'id' => 'treatmentDetailsModal'])
@include('hmo.approvalModal', ['title' => 'Approve Medication/Treatment', 'isUpdate' => false, 'id' => 'approvalModal']) --}}
@include('admin.modals.sponsorCategoryModal', ['title' => 'New Sponsor Category', 'isUpdate' => false, 'id' => 'newSponsorCategoryModal'])
@include('admin.modals.sponsorCategoryModal', ['title' => 'Edit Sponsor Category', 'isUpdate' => true, 'id' => 'updateSponsorCategoryModal'])

    <div class="container mt-5 bg-white">
        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-verifyPatients-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-verifyPatients" type="button" role="tab" aria-controls="nav-verifyPatients"
                        aria-selected="true">Sponsor Category</button>

                    <button class="nav-link" id="nav-treatments-tab" data-bs-toggle="tab" data-bs-target="#nav-treatments"
                        type="button" role="tab" aria-controls="nav-treatments" aria-selected="false">Treatments</button>

                    <button class="nav-link" id="nav-bills-tab" data-bs-toggle="tab" data-bs-target="#nav-bills"
                        type="button" role="tab" aria-controls="nav-bills" aria-selected="false">Bills</button>

                    <button class="nav-link" id="nav-reporst-tab" data-bs-toggle="tab" data-bs-target="#nav-reports"
                        type="button" role="tab" aria-controls="nav-reports" aria-selected="false">Reports</button>
                </div>
            </nav>
            <div class="tab-content px-2" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-verifyPatients" role="tabpanel"
                    aria-labelledby="nav-verifyPatients-tab" tabindex="0">
                    
                    <div class="text-start my-4">
                        <button class="btn btn-primary" type="button" id="addSponsnorCategoryBtn">
                            <i class="bi bi-plus-circle"></i>
                            Category
                        </button>
                    </div>

                    <div class="py-2">
                        <table id="sponsorCategoryTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Descrption</th>
                                    <th>Consultation</th>
                                    <th>Pay Class</th>
                                    <th>Approval</th>
                                    <th>Bill Matrix</th>
                                    <th>Pay Bal?</th>
                                    <th>Created</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- treatments table -->
                <div class="tab-pane fade" id="nav-treatments" role="tabpanel" aria-labelledby="nav-treatments-tab"
                    tabindex="0">
                    <div class="py-4">
                        <table id="treatmentsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09/10/2023</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>Axe Mansard</td>
                                    <td>Dr Toby</td>
                                    <td>F12Z-Acute Spundolosis</td>
                                    <td>Out-Patient</td>
                                    <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Details</button>
                                        {{-- <button class="btn btn-outline-primary" id="reviewConsultationBtn">Approve</button> --}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- bills table -->
                <div class="tab-pane fade" id="nav-bills" role="tabpanel" aria-labelledby="nav-bills-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <table id="sponsorsTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Bill Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09/10/2023</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>Axe Mansard</td>
                                    <td>Dr Toby</td>
                                    <td>F12Z-Acute Spundolosis</td>
                                    <td>Out-Patient</td>
                                    <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Sent</button>
                                        {{-- <button class="btn btn-outline-primary" id="reviewConsultationBtn">Approve</button> --}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- reports table -->
                <div class="tab-pane fade" id="nav-reports" role="tabpanel" aria-labelledby="nav-reports-tab"
                    tabindex="0">
                    <div class="py-4 justify-content-center">
                        <table id="reportsTable" class="table table-hover align-center table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Verified</th>
                                    <th>Treatment</th>
                                    <th>Bill-Sent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09/10/2023</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>Axe Mansard</td>
                                    <td>Dr Toby</td>
                                    <td>F12Z-Acute Spundolosis</td>
                                    <td>Out-Patient</td>
                                    <td class="fst-italic">Pending</td>
                                    <td class="fst-italic">No Code</td>
                                    <td class="fst-italic">Not Sent</td>
                                    {{-- <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Sent</button>
                                    </td> --}}
                                </tr>
                                <tr>
                                    <td>08/10/2023</td>
                                    <td>SH21/1403 Shine Ewara</td>
                                    <td>Health Partners</td>
                                    <td>Dr Tony</td>
                                    <td>F12Z-Severe Malaria</td>
                                    <td>In-Patient</td>
                                    <td class="fst-italic">Verified</td>
                                    <td class="fst-italic">HP-45srt6if1</td>
                                    <td class="fst-italic">Sent</td>
                                    {{-- <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Sent</button>
                                    </td> --}}
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
