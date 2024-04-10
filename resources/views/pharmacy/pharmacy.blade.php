@extends('layout')

@section('content')
@vite(['resources/js/pharmacy.js'])

@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isAnc' => false, 'isLab' => false, 'isHmo' => true, 'id' => 'treatmentDetailsModal'])
@include('nurses.treatmentDetailsModal', ['title' => 'ANC Treatment Details', 'isAnc' => true, 'isLab' => false, 'isHmo' => true, 'id' => 'ancTreatmentDetailsModal'])
@include('investigations.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])
@include('pharmacy.billingDispenseModal', ['title' => "Patient's Billing & Dispense", 'isEdit' => false, 'id' => 'billingDispenseModal'])
@include('extras.bulkRequestModal', ['title' => 'Bulk Request', 'dept' => 'Pharmacy', 'isPharmacy' => true, 'id' => 'bulkRequestModal'])

    <div class="container p-1 mt-5 bg-white">     
        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-outPatients-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-outPatients" type="button" role="tab" aria-controls="nav-outPatients"
                        aria-selected="true">OutPatients</button>

                    <button class="nav-link" id="nav-inPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-inPatients"
                        type="button" role="tab" aria-controls="nav-inPatients" aria-selected="false">Inpatients</button>

                    <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients"
                        type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>

                    <button class="nav-link" id="nav-expirationStock-tab" data-bs-toggle="tab" data-bs-target="#nav-expirationStock"
                        type="button" role="tab" aria-controls="nav-expirationStock" aria-selected="false">Expiration/Stock</button>

                    <button class="nav-link" id="nav-bulkRequests-tab" data-bs-toggle="tab" data-bs-target="#nav-bulkRequests"
                    type="button" role="tab" aria-controls="nav-bulkRequests" aria-selected="false">Bulk Requests</button>

                    <button class="nav-link" id="nav-emergency-tab" data-bs-toggle="tab" data-bs-target="#nav-emergency"
                    type="button" role="tab" aria-controls="nav-emergency" aria-selected="false">Emergency</button>
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
                                    <th>Ward</th>
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
                        <table id="bulkRequestsTable" class="table table-sm bulkRequestsTable">
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
                <!-- emergency medication table -->
                <div class="tab-pane fade" id="nav-emergency" role="tabpanel" aria-labelledby="nav-emergency-tab" tabindex="0">
                    <div class="py-4 ">
                        <table id="emergencyTable" class="table table-hover align-middle table-sm emergencyTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Item</th>
                                    <th>Prescription</th>
                                    <th>Qty</th>
                                    <th>Prescribed By</th>
                                    <th>Note</th>
                                    <th>DOC</th>
                                    <th></th>
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