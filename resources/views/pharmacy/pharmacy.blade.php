@extends('layout')

@section('content')
@vite(['resources/css/colourblink.scss', 'resources/js/pharmacy.js'])

@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isAnc' => false, 'isLab' => false, 'isHmo' => true, 'id' => 'treatmentDetailsModal'])
@include('nurses.treatmentDetailsModal', ['title' => 'ANC Treatment Details', 'isAnc' => true, 'isLab' => false, 'isHmo' => true, 'id' => 'ancTreatmentDetailsModal'])
@include('investigations.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])
@include('pharmacy.billingDispenseModal', ['title' => "Patient's Billing & Dispense", 'isEdit' => false, 'id' => 'billingDispenseModal'])
@include('extras.bulkRequestModal', ['title' => 'Bulk Request', 'dept' => 'Pharmacy', 'isPharmacy' => true, 'id' => 'bulkRequestModal'])
@include('extras.theatreStockModal', ['title' => 'Resolve Theatre Stock', 'id' => 'theatreStockModal'])
@include('extras.bulkRequestModal', ['title' => 'Theatre Request', 'dept' => 'Theatre', 'isPharmacy' => false, 'id' => 'theatreRequestModal'])
@include('extras.shiftReportTemplateModal', ['title' => 'New Report', 'isUpdate' => false, 'dept' => 'pharmacy', 'isView' => false, 'id' => 'newShiftReportTemplateModal'])
@include('extras.shiftReportTemplateModal', ['title' => 'Edit Report', 'isUpdate' => true, 'dept' => 'pharmacy', 'isView' => false, 'id' => 'editShiftReportTemplateModal'])
@include('extras.shiftReportTemplateModal', ['title' => 'View Report', 'isUpdate' => false, 'dept' => 'pharmacy', 'isView' => true, 'id' => 'viewShiftReportTemplateModal'])

    <div class="container p-1 mt-5 bg-white">   
        <div class="offcanvas offcanvas-end overflow-auto" data-bs-scroll="true" tabindex="-1" id="emergencyListOffcanvas"
            aria-labelledby="emergencyListOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="emergencyListOffcanvasLabel">Emergency List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="py-4 ">
                    <table id="emergencyTable" class="table table-hover table-sm emergencyTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th>Medication/Item</th>
                                <th>Prescription</th>
                                <th>Qty</th>
                                <th>By</th>
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
        <div class="offcanvas offcanvas-start overflow-auto" data-bs-scroll="true" tabindex="-1" id="shiftReportOffcanvas"
            aria-labelledby="shiftReportOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="shiftReportOffcanvasLabel">Shift Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="py-4 ">
                    <div class="text-start py-4">
                        <button type="button" id="newPharmacyReportBtn" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            New Shift Report
                        </button>
                    </div>
                    <table id="pharmacyShiftReportTable" class="table table-sm pharmacyShiftReportTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Shift</th>
                                <th>Written By</th>
                                <th>Viewed</th>
                                <th>Viewed By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-start mb-4">
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" id="emergencyListBtn"
                data-bs-target="#emergencyListOffcanvas" aria-controls="emergencyListOffcanvas">
                <i class="bi bi-list-check"></i>
                Emergency Rx <span class="badge text-bg-danger" id="emergencyListCount"></span>
            </button>
            <button type="button" id="shiftReportBtn" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#shiftReportOffcanvas" aria-controls="emergencyListOffcanvas">
                Shift Reports <span class="badge text-bg-danger" id="shiftBadgeSpan"></span>
            </button>
        </div>
        
        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-inPatients-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-inPatients-view" type="button" role="tab" aria-controls="nav-inPatients"
                        aria-selected="true">Inpatients</button>

                    <button class="nav-link" id="nav-outPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-outPatients-view"
                        type="button" role="tab" aria-controls="nav-outPatients" aria-selected="false">OutPatients</button>

                    <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients-view"
                        type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>

                    <button class="nav-link" id="nav-bulkRequests-tab" data-bs-toggle="tab" data-bs-target="#nav-bulkRequests"
                    type="button" role="tab" aria-controls="nav-bulkRequests" aria-selected="false">Bulk Requests</button>

                    <button class="nav-link" id="nav-theatreRequests-tab" data-bs-toggle="tab" data-bs-target="#nav-theatreRequests"
                    type="button" role="tab" aria-controls="nav-theatreRequests" aria-selected="false">Theatre Requests</button>

                    <button class="nav-link" id="nav-lowStock-tab" data-bs-toggle="tab" data-bs-target="#nav-lowStock"
                        type="button" role="tab" aria-controls="nav-lowStock" aria-selected="false">Low Stock</button>

                    <button class="nav-link" id="nav-expirationStock-tab" data-bs-toggle="tab" data-bs-target="#nav-expirationStock"
                        type="button" role="tab" aria-controls="nav-expirationStock" aria-selected="false">Expiring Stock</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-inPatients-view" role="tabpanel"
                    aria-labelledby="nav-inPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="inPatientsTable" class="table table-hover align-middle table-sm">
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
                <!-- active table -->
                <div class="tab-pane fade" id="nav-outPatients-view" role="tabpanel" aria-labelledby="nav-outPatients-tab"
                    tabindex="0">
                    <div class="py-4">
                        <table id="outPatientsTable" class="table table-hover table-sm">
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
                <div class="tab-pane fade" id="nav-ancPatients-view" role="tabpanel" aria-labelledby="nav-ancPatients-tab"
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
                <!-- theatre request table -->
                <div class="tab-pane fade" id="nav-theatreRequests" role="tabpanel" aria-labelledby="nav-theatreRequests-tab" tabindex="0">
                    <div class="text-start py-4">
                        <button type="button" id="newTheatreRequestBtn" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            Theatre Request
                        </button>
                    </div>
                    <div class="pt-2 ">
                        <table id="theatreRequestsTable" class="table table-hover align-middle table-sm theatreRequestsTable">
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
                 <!-- Low stock table -->
                <div class="tab-pane fade" id="nav-lowStock" role="tabpanel" aria-labelledby="nav-lowStock-tab"
                    tabindex="0">
                    {{-- <x-form-div class="col-md-4 pt-2">
                        <x-input-span id="filterListLabel">Display List<x-required-span /></x-input-span>
                        <select class="form-select form-select-md" name="filterList" id="filterList">
                            <option value="expiration">Close Expiration</option>
                            <option value="stockLevel">Low Stock</option>
                        </select>
                    </x-form-div> --}}
                    <div class="pt-2 ">
                        <table id="lowStockTable" class="table table-hover table-sm lowStockTable">
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Location</th>
                                    <th>Stock Level</th>
                                    <th>Reorder Level</th>
                                    <th>Selling Price</th>
                                    <th>Expiring in</th>
                                    <th>Times Prescribed(30days)</th>
                                    <th>Times Dispensed(30days)</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                 <!-- Near Expiration table -->
                <div class="tab-pane fade" id="nav-expirationStock" role="tabpanel" aria-labelledby="nav-expirationStock-tab"
                    tabindex="0">
                    {{-- <x-form-div class="col-md-4 pt-2">
                        <x-input-span id="filterListLabel">Display List<x-required-span /></x-input-span>
                        <select class="form-select form-select-md" name="filterList" id="filterList">
                            <option value="expiration">Close Expiration</option>
                            <option value="stockLevel">Low Stock</option>
                        </select>
                    </x-form-div> --}}
                    <div class="pt-2 ">
                        <table id="expirationStockTable" class="table table-hover table-sm expirationStockTable">
                            <thead>
                                <tr>
                                    <th>Medication</th>
                                    <th>Location</th>
                                    <th>Stock Level</th>
                                    <th>Reorder Level</th>
                                    <th>Selling Price</th>
                                    <th>Expiring in</th>
                                    <th>Times Prescribed(30days)</th>
                                    <th>Times Dispensed(30days)</th>
                                    <th>Quantity</th>
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