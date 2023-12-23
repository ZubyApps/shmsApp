@extends('layout')

@section('content')
@vite(['resources/js/hmo.js'])

@include('hmo.verifyModal', ['title' => 'Verify Patient', 'isUpdate' => false, 'id' => 'verifyModal'])
@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isLab' => false, 'isHmo' => true, 'id' => 'treatmentDetailsModal'])
@include('hmo.approvalModal', ['title' => 'Approve Medication/Treatment', 'isUpdate' => false, 'id' => 'approvalModal'])
@include('hmo.makeBillModal', ['title' => "Make Patient's Bill", 'isEdit' => false, 'id' => 'makeBillModal'])
@include('investigations.investigationsModal', ['title' => 'Investigations', 'isDoctor' => true, 'id' => 'investigationsModal'])



    <div class="container p-1 mt-5 bg-white">
        <div class="offcanvas offcanvas-start overflow-auto" data-bs-scroll="true" tabindex="-1" id="waitingListOffcanvas2"
        aria-labelledby="waitingListOffcanvasLabel" aria-expanded="false">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary" id="waitingListOffcanvasLabel">List of Waiting Patients</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="py-4 ">
                <table id="waitingTable" class="table table-hover align-middle table-sm bg-primary">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Sex</th>
                            <th>Age</th>
                            <th>Sponsor</th>
                            <th>Came</th>
                            <th>Dr</th>
                            <th>Vitals</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

        <div class="offcanvas offcanvas-top overflow-auto" data-bs-scroll="true" tabindex="-1" id="approvalListOffcanvas"
            aria-labelledby="approvalListOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="approvalListOffcanvasLabel">Approval List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="p-2 form-control">
                    <table id="approvalListTable" class="table table-hover align-middle table-sm approvalListTable">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Sponsor</th>
                                <th>Dr</th>
                                <th>Prescribed</th>
                                <th>Diagnosis</th>
                                <th>Treatment</th>
                                <th>Prescription</th>
                                <th>Qty</th>
                                <th>Bill</th>
                                <th>Billed</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-start mb-4">
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="waitingBtn" data-bs-target="#waitingListOffcanvas2" aria-controls="waitingListOffcanvas2">
                <i class="bi bi-list-check"></i>
                Waiting List
            </button>
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="approvalListBtn"
                data-bs-target="#approvalListOffcanvas" aria-controls="approvalListOffcanvas">
                <i class="bi bi-list-check"></i>
                Approval List
            </button>
        </div>

        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-verifyPatients-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-verifyPatients" type="button" role="tab" aria-controls="nav-verifyPatients"
                        aria-selected="true">Verification</button>

                    <button class="nav-link" id="nav-treatments-tab" data-bs-toggle="tab" data-bs-target="#nav-treatments"
                        type="button" role="tab" aria-controls="nav-treatments" aria-selected="false">Treatments</button>

                    <button class="nav-link" id="nav-billpatients-tab" data-bs-toggle="tab" data-bs-target="#nav-billpatients"
                        type="button" role="tab" aria-controls="nav-billpatients" aria-selected="false">Bill</button>

                    <button class="nav-link" id="nav-reporst-tab" data-bs-toggle="tab" data-bs-target="#nav-reports"
                        type="button" role="tab" aria-controls="nav-reports" aria-selected="false">Reports</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-verifyPatients" role="tabpanel"
                    aria-labelledby="nav-verifyPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="verificationTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Came</th>
                                    <th>Patient</th>
                                    <th>Sex</th>
                                    <th>Age</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- treatments table -->
                <div class="tab-pane fade" id="nav-treatments" role="tabpanel" aria-labelledby="nav-treatments-tab" tabindex="0">
                    <x-form-div class="col-md-4 pt-2">
                        <x-input-span id="filterListLabel">Filter List<x-required-span /></x-input-span>
                        <select class="form-select form-select-md" name="filterList" id="filterList">
                            <option value="">All</option>
                            <option value="Outpatient">Outpatients</option>
                            <option value="Inpatient">Inpatients</option>
                            <option value="ANC">ANC</option>
                        </select>
                    </x-form-div>
                    <div class="py-2">
                        <table id="hmoTreatmentsTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Investigations</th>
                                    {{-- <th>Vitals</th> --}}
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- bills table -->
                <div class="tab-pane fade" id="nav-billpatients" role="tabpanel" aria-labelledby="nav-billpatients-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <table id="billPatientsTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Make Bill</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- <tr>
                                    <td>09/10/2023</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>Axe Mansard</td>
                                    <td>Dr Toby</td>
                                    <td>F12Z-Acute Spundolosis</td>
                                    <td>Out-Patient</td>
                                    <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Sent</button>
                                        <button class="btn btn-outline-primary" id="reviewConsultationBtn">Approve</button>
                                    </td>
                                </tr> --}}
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
