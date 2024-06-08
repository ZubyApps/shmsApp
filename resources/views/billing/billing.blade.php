@extends('layout')

@section('content')
@vite(['resources/css/colourblink.scss', 'resources/js/billing.js'])

@include('investigations.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])
@include('billing.billingModal', ['title' => 'Billing Details', 'isUpdate' => false, 'id' => 'billingModal'])
@include('billing.dischargeBillModal', ['title' => 'Add Discharge Bill', 'id' => 'dischargeBillModal'])
@include('billing.outstandingBillsModal', ['title' => "Patient's Outstanding Bills", 'isUpdate' => false, 'id' => 'outstandingBillsModal'])
@include('billing.billModal', ['title' => "", 'isSummary' => true, 'id' => 'billModal'])
@include('billing.expenseModal', ['title' => "New Expense", 'isUpdate' => false, 'isManagement' => false, 'id' => 'newExpenseModal'])
@include('billing.expenseModal', ['title' => "Update Expense", 'isUpdate' => true, 'isManagement' => false, 'id' => 'updateExpenseModal'])
@include('billing.thirdPartyServiceModal', ['title' => "Initiate Third Party Service", 'id' => 'thirdPartyServiceModal'])
@include('extras.shiftReportTemplateModal', ['title' => 'New Report', 'isUpdate' => false, 'dept' => 'billing', 'isView' => false, 'id' => 'newShiftReportTemplateModal'])
@include('extras.shiftReportTemplateModal', ['title' => 'Edit Report', 'isUpdate' => true, 'dept' => 'billing', 'isView' => false, 'id' => 'editShiftReportTemplateModal'])
@include('extras.shiftReportTemplateModal', ['title' => 'View Report', 'isUpdate' => false, 'dept' => 'billing', 'isView' => true, 'id' => 'viewShiftReportTemplateModal'])

    <div class="container mt-5 bg-white">

        <div class="container p-1 mt-5 bg-white">
            <div class="offcanvas offcanvas-start overflow-auto" data-bs-scroll="true" tabindex="-1" id="waitingListOffcanvas2" aria-labelledby="waitingListOffcanvasLabel" aria-expanded="false">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title text-primary" id="waitingListOffcanvasLabel">List of Waiting Patients</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
            <div class="offcanvas-body">
                <div class="form-control mb-2">
                    <h5>Average Waiting Time</h5>
                    <x-form-div class="col-xl-12">
                        <x-input-span class="">Last Month</x-input-span>
                        <x-form-input name="lastMonth" id="lastMonth" readonly/>
                        <x-input-span class="">This Month</x-input-span>
                        <x-form-input name="thisMonth" id="thisMonth" readonly/>
                    </x-form-div>
                    <x-form-div class="col-xl-12">
                        <x-input-span class="">Last Week</x-input-span>
                        <x-form-input name="lastWeek" id="lastWeek" readonly/>
                        <x-input-span class="">This Week</x-input-span>
                        <x-form-input name="thisWeek" id="thisWeek" readonly/>
                    </x-form-div>
                </div>
                <div class="py-3 form-control">
                    <table id="waitingTable" class="table align-middle table-sm bg-primary">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Sex</th>
                                <th>Age</th>
                                <th>Sponsor</th>
                                <th>Came</th>
                                <th>For</th>
                                <th>Seeing</th>
                                <th>Vitals</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-top" data-bs-scroll="true" tabindex="-1" id="offcanvasInvestigations"
            aria-labelledby="offcanvasInvestigationsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasInvestigations">Outpatient Investigations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="my-2">
                    <div class="row overflow-auto m-1">
                        <table id="outpatientInvestigationsTable" class="table table-sm">
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
        </div>

        <div class="offcanvas offcanvas-end overflow-auto" data-bs-scroll="true" tabindex="-1" id="shiftReportOffcanvas"
            aria-labelledby="shiftReportOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="shiftReportOffcanvasLabel">Shift Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="py-4 ">
                    <div class="text-start py-4">
                        <button type="button" id="newBillingReportBtn" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>
                            New Shift Report
                        </button>
                    </div>
                    <table id="billingShiftReportTable" class="table table-sm billingShiftReportTable">
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
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="waitingBtn" data-bs-target="#waitingListOffcanvas2" aria-controls="waitingListOffcanvas2">
                <i class="bi bi-list-check"></i>
                Waiting List
            </button>
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="outpatientsInvestigationBtn"
                data-bs-target="#offcanvasInvestigations" aria-controls="offcanvasInvestigations">
                <i class="bi bi-list-check"></i>
                Outpatient Investigations
            </button>
            <button type="button" id="shiftReportBtn" class="btn btn-primary" data-bs-toggle="offcanvas" data-bs-target="#shiftReportOffcanvas" aria-controls="emergencyListOffcanvas">
                Shift Reports <span class="badge text-bg-danger" id="shiftBadgeSpan"></span>
            </button>
        </div>

        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-outPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-outPatients" 
                        type="button" role="tab" aria-controls="nav-outPatients" aria-selected="true">OutPatients</button>
    
                    <button class="nav-link" id="nav-inPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-inPatients"
                        type="button" role="tab" aria-controls="nav-inPatients" aria-selected="false">Inpatients</button>
    
                    <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients"
                        type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>

                    <button class="nav-link" id="nav-openVisits-tab" data-bs-toggle="tab" data-bs-target="#nav-openVisits"
                        type="button" role="tab" aria-controls="nav-openVisits" aria-selected="false">Open Visits</button>

                    <button class="nav-link" id="nav-expenses-tab" data-bs-toggle="tab" data-bs-target="#nav-expenses"
                        type="button" role="tab" aria-controls="nav-expenses" aria-selected="false">Expenses</button>

                    <button class="nav-link" id="nav-balancing-tab" data-bs-toggle="tab" data-bs-target="#nav-balancing"
                        type="button" role="tab" aria-controls="nav-balancing" aria-selected="false">Balancing</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-outPatients" role="tabpanel"
                    aria-labelledby="nav-outPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="outPatientsVisitTable" class="table align-middle table-sm">
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
                </div>
                <!-- inpatients table -->
                <div class="tab-pane fade" id="nav-inPatients" role="tabpanel" aria-labelledby="nav-inPatients-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <table id="inPatientsVisitTable" class="table align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Status</th>
                                    <th>Ward</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- Anc table -->
                <div class="tab-pane fade" id="nav-ancPatients" role="tabpanel" aria-labelledby="nav-ancPatients-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <table id="ancPatientsVisitTable" class="table align-middle table-sm">
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
                </div>
                <!-- Open Records table -->
                <div class="tab-pane fade" id="nav-openVisits" role="tabpanel" aria-labelledby="nav-openVisits-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <table id="openVisitsTable" class="table table-sm">
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
                </div>
                <!-- Expenses table -->
                <div class="tab-pane fade" id="nav-expenses" role="tabpanel" aria-labelledby="nav-expenses-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <div class="text-start py-3">
                            <button type="button" id="newExpenseBtn" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Expense
                            </button>
                        </div>
                        <table id="expensesTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Desription</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Given to</th>
                                    <th>Given By</th>
                                    <th>Approved By</th>
                                    <th>Comment</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="">
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold">Total</td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- Balancing table -->
                <div class="tab-pane fade" id="nav-balancing" role="tabpanel" aria-labelledby="nav-balancing-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <x-form-div class="col-xl-4 py-3 balancingDateDiv">
                            <x-input-span class="">Pick Date</x-input-span>
                            <x-form-input type="date" name="balanceDate" id="balanceDate" />
                            <button class="input-group-text searchBalanceByDateBtn">Search</button>
                        </x-form-div>
                        <table id="balancingTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Total Cash</th>
                                    <th>Total Expense</th>
                                    <th>Balance</th>
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