@extends('layout')

@section('content')
@vite(['resources/js/billing.js'])

@include('hmo.treatmentDetailsModal', ['title' => 'Treatment Details', 'isUpdate' => false, 'id' => 'treatmentDetailsModal'])
@include('investigations.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])

    <div class="container p-1 mt-5 bg-white">
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasWithBothOptionsLabel">List of Waiting Patients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="py-4 ">
                    <table id="waitingListTable" class="table table-hover align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SH21/4012 Joesphine Ene Odeh</td>
                                <td>Avon HMO</td>
                                <td><span class="badge rounded-pill text-bg-secondary p-2">Waiting</span></td>
                                <td><i class="btn btn-outline-none text-primary bi bi-x-circle"></i></td>
                            </tr>
                            <tr>
                                <td>SH23/7865 Patrick Abiodun Aso</td>
                                <td>Axe Mansard HMO</td>
                                <td><span class="badge rounded-pill text-bg-light p-2">Dr Toby</span></td>
                                <td>
                                    {{-- <i class="btn btn-outline-none text-primary bi bi-x-circle"></i> --}}
                                </td>
                            </tr>
                            <tr>
                                <td>SH21/4012 John Okoro</td>
                                <td>Avon HMO</td>
                                <td><span class="badge rounded-pill text-bg-light p-2">Dr Bisoye</span></td>
                                <td>
                                    {{-- <i class="btn btn-outline-none text-primary bi bi-x-circle"></i> --}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-top" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions2"
            aria-labelledby="offcanvasWithBothOptions2Label">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasWithBothOptions2Label2">Patients Investigation Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="my-2 form-control">
                    <span class="fw-bold text-primary"> Investigations </span>
                    <div class="row overflow-auto m-1">
                        <table id="investigationTable" class="table table-hover align-middle table-sm bg-primary">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Requested</th>
                                    <th>Physician</th>
                                    <th>Patient</th>
                                    <th>Diagnosis</th>
                                    <th>Investigation</th>
                                    <th>Result</th>
                                    <th>Date</th>
                                    <th>Staff</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Lab</td>
                                    <td>12/9/23 11:02pm</td>
                                    <td>Dr Toby</td>
                                    <td>SH23/7865 Patrick Abiodun Aso</td>
                                    <td>Malaria Unspecified</td>
                                    <td>Malaria Parasite</td>
                                    <td>Pfall ++</td>
                                    <td>12/09/23</td>
                                    <td>Onjefu</td>
                                    <td>
                                        <div class="dropdown">
                                            <i class="bi bi-gear fs-4" role="button" data-bs-toggle="dropdown"></i>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item edit-investigation-btn" href="#" data-id="">
                                                        <i class="bi bi-pencil-fill"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item delete-investigation-btn" href="#" data-id="">
                                                        <i class="bi bi-trash3-fill"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lab</td>
                                    <td>12/9/23 11:02pm</td>
                                    <td>Dr Toby</td>
                                    <td>SH23/0129 John Aba</td>
                                    <td>Gastroenterities</td>
                                    <td>FBC</td>
                                    <td>
                                    Abcd wxyz 2341 bhuh nska manha LKLLJ
                                    Abcd wxyz 2341 bhuh nska manha LKLLJ
                                    Abcd wxyz 2341 bhuh nska manha LKLLJ
                                    </td>
                                    <td>12/10/23 11:23am</td>
                                    <td>Onjefu</td>
                                    <td>
                                        <div class="dropdown">
                                            <i class="bi bi-gear fs-4" role="button" data-bs-toggle="dropdown"></i>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item edit-investigation-btn" href="#" data-id="">
                                                        <i class="bi bi-pencil-fill"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item delete-investigation-btn" href="#" data-id="">
                                                        <i class="bi bi-trash3-fill"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Lab</td>
                                    <td>12/9/23 11:02pm</td>
                                    <td>Dr Emmanuel</td>
                                    <td>SH22/0024 Gabrial Omaji</td>
                                    <td>Acute Spondulosis</td>
                                    <td>Malaria Parasite</td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>
                                        <div class="dropdown">
                                            <i class="bi bi-gear fs-4" role="button" data-bs-toggle="dropdown"></i>

                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item addResultBtn" id="addResultBtn" href="#" data-id="">
                                                    <i class="bi bi-plus-square"></i> Add Result
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item edit-result-btn" href="#" data-id="">
                                                        <i class="bi bi-pencil-fill"></i> Edit
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item delete-result-btn" href="#" data-id="">
                                                        <i class="bi bi-trash3-fill"></i> Delete
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-start mb-4">
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
                <i class="bi bi-list-check"></i>
                Waiting List
            </button>
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions2" aria-controls="offcanvasWithBothOptions2">
                <i class="bi bi-list-check"></i>
                Patients Investigation Table
            </button>
        </div>

        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-yourPatients-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-yourPatients" type="button" role="tab" aria-controls="nav-yourPatients"
                        aria-selected="true">All Patients</button>

                    <button class="nav-link" id="nav-allPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-allPatients"
                        type="button" role="tab" aria-controls="nav-allPatients" aria-selected="false">Out-Patients</button>

                    <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                        type="button" role="tab" aria-controls="nav-profile" aria-selected="false">In-Patients</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-yourPatients" role="tabpanel"
                    aria-labelledby="nav-yourPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="yourPatientsTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>20/09/23</td>
                                    <td>SH23/7865 Patrick Abiodun Aso</td>
                                    <td>QC30-Malingering</td>
                                    <td>Axe Mansard HMO</td>
                                    <td>Out-patient</td>
                                    <td><button class="btn btn-outline-primary" id="treatmentDetailsBtn">Details</button></td>
                                </tr>
                                <tr>
                                    <td>21/05/22</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>QC30-Malingering</td>
                                    <td>Self</td>
                                    <td>In-patient</td>
                                    <td><button class="btn btn-outline-primary">Details</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- active table -->
                <div class="tab-pane fade" id="nav-allPatients" role="tabpanel" aria-labelledby="nav-allPatients-tab"
                    tabindex="0">
                    <div class="py-4">
                        <table id="allPatientsTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- sponsors table -->
                {{-- <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <table id="sponsorsTable" class="table table-hover align-middle table-sm">
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
                        </table>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>

@endsection