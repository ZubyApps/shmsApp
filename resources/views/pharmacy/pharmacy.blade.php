@extends('layout')

@section('content')
@vite(['resources/js/pharmacy.js'])

@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isAnc' => false, 'isLab' => false, 'isHmo' => true, 'id' => 'treatmentDetailsModal'])
@include('nurses.treatmentDetailsModal', ['title' => 'ANC Treatment Details', 'isAnc' => true, 'isLab' => false, 'isHmo' => true, 'id' => 'ancTreatmentDetailsModal'])
@include('investigations.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])
@include('pharmacy.billingDispenseModal', ['title' => "Patient's Billing & Dispense", 'isEdit' => false, 'id' => 'billingDispenseModal'])
@include('extras.bulkRequestModal', ['title' => 'Bulk Request', 'dept' => 'Pharmacy', 'isPharmacy' => true, 'id' => 'bulkRequestModal'])

    <div class="container p-1 mt-5 bg-white">

        {{-- <div class="offcanvas offcanvas-top" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions2"
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
        </div> --}}

        <div class="text-start mb-4">
            {{-- <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
                <i class="bi bi-list-check"></i>
                Waiting List
            </button> --}}
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions2" aria-controls="offcanvasWithBothOptions2">
                <i class="bi bi-list-check"></i>
                Patients Investigation Table
            </button>
        </div>

        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-outPatients-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-outPatients" type="button" role="tab" aria-controls="nav-outPatients"
                        aria-selected="true">OutPatients</button>

                    <button class="nav-link" id="nav-inPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-inPatients"
                        type="button" role="tab" aria-controls="nav-inPatients" aria-selected="false">In patients</button>

                    <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients"
                        type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>

                    <button class="nav-link" id="nav-expirationStock-tab" data-bs-toggle="tab" data-bs-target="#nav-expirationStock"
                        type="button" role="tab" aria-controls="nav-expirationStock" aria-selected="false">Expiration/Stock</button>

                    <button class="nav-link" id="nav-bulkRequests-tab" data-bs-toggle="tab" data-bs-target="#nav-bulkRequests"
                    type="button" role="tab" aria-controls="nav-bulkRequests" aria-selected="false">Bulk Requests</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-outPatients" role="tabpanel"
                    aria-labelledby="nav-outPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="outPatientsTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Billing/Dispense</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- active table -->
                <div class="tab-pane fade" id="nav-inPatients" role="tabpanel" aria-labelledby="nav-inPatients-tab"
                    tabindex="0">
                    <div class="py-4">
                        <table id="inPatientsTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Billing/Dispense</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- ANC patients table -->
                <div class="tab-pane fade" id="nav-ancPatients" role="tabpanel" aria-labelledby="nav-ancPatients-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <table id="ancPatientsTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Billing/Dispense</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- Near Expiration table -->
                <div class="tab-pane fade" id="nav-expirationStock" role="tabpanel" aria-labelledby="nav-expirationStock-tab"
                    tabindex="0">
                    <x-form-div class="col-md-4 pt-2">
                        <x-input-span id="filterListLabel">Display List<x-required-span /></x-input-span>
                        <select class="form-select form-select-md" name="filterList" id="filterList">
                            <option value="expiration">Close Expiration</option>
                            <option value="stockLevel">Low Stock</option>
                        </select>
                    </x-form-div>
                    <div class="pt-2 ">
                        <table id="expirationStockTable" class="table table-hover table-sm expirationStockTable">
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Stock Level</th>
                                    <th>Reorder Level</th>
                                    <th>Selling Price</th>
                                    <th>Expiring in</th>
                                    <th>Times Prescribed(30days)</th>
                                    <th>Times Dispensed(30days)</th>
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
                                    <th>Quantity</th>
                                    <th>Dept</th>
                                    <th>Requested By</th>
                                    <th>Note</th>
                                    <th>Approved By</th>
                                    <th>Dispensed By</th>
                                    <th>Dispensed</th>
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