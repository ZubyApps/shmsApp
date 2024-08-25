@extends('layout')
<meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
@section('content')
@vite(['resources/css/colourblink.scss', 'resources/js/hmo.js'])

@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isAnc' => false, 'isLab' => false, 'isHmo' => true, 'id' => 'treatmentDetailsModal'])
@include('nurses.treatmentDetailsModal', ['title' => 'ANC Treatment Details', 'isAnc' => true, 'isLab' => false, 'isHmo' => true, 'id' => 'ancTreatmentDetailsModal'])
@include('hmo.verifyModal', ['title' => 'Verify Patient', 'isUpdate' => false, 'id' => 'verifyModal'])
@include('hmo.changeSponsorModal', ['title' => "Change Patient's Visit's Sponsor", 'id' => 'changeSponsorModal'])
@include('hmo.approvalModal', ['title' => 'Approve Medication/Treatment', 'isUpdate' => false, 'id' => 'approvalModal'])
@include('hmo.makeBillModal', ['title' => "Make Patient's Bill", 'isEdit' => false, 'id' => 'makeBillModal'])
@include('investigations.investigationsModal', ['title' => 'Investigations', 'isDoctor' => true, 'id' => 'investigationsModal'])
@include('extras.labResultModal', ['title' => 'Lab Result', 'dept' => 'Lab', 'isPharmacy' => false, 'id' => 'labResultModal'])
@include('hmo.reconciliationModal', ['title' => 'Reconciliation', 'id' => 'reconciliationModal'])
@include('extras.medicalReportListModal', ['title' => 'Medical Report List', 'isDoctor' => false, 'id' => 'medicalReportListModal' ])
@include('extras.viewMedicalReportModal', ['title' => '', 'isUpdate' => true, 'id' => 'viewMedicalReportModal' ])
@include('hmo.capitationPaymentModal', ['title' => '', 'id' => 'capitationPaymentModal' ])
@include('hmo.registerBillSentModal', ['title' => 'Register Bill As Sent', 'id' => 'registerBillSentModal' ])



    <div class="container p-1 mt-5 bg-white">
        <div class="offcanvas offcanvas-start overflow-auto" data-bs-scroll="true" tabindex="-1" id="waitingListOffcanvas2"
        aria-labelledby="waitingListOffcanvasLabel" aria-expanded="false">
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
                <table id="waitingTable" class="table table-hover align-middle table-sm bg-primary">
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

        <div class="offcanvas offcanvas-top overflow-auto" data-bs-scroll="true" tabindex="-1" id="hmoApprovalListOffcanvas"
            aria-labelledby="hmoApprovalListOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="hmoApprovalListOffcanvasLabel">PHIS Approval List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="p-2 form-control">
                    <fieldset id="hmoApprovalFieldset">
                        <table id="hmoApprovalListTable" class="table table-hover align-middle table-sm">
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
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-top overflow-auto" data-bs-scroll="true" tabindex="-1" id="nhisApprovalListOffcanvas"
            aria-labelledby="nhisApprovalListOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="nhisApprovalListOffcanvasLabel">NHIS Approval List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="p-2 form-control">
                    <fieldset id="nhisApprovalFieldset">
                        <table id="nhisApprovalListTable" class="table table-hover align-middle table-sm">
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
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-top overflow-auto" data-bs-scroll="true" tabindex="-1" id="dueRemindersListOffcanvas"
            aria-labelledby="dueRemindersListOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="dueRemindersListOffcanvasLabel">Due Reminders List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="p-2 form-control">
                    <fieldset id="dueRemindersFieldset">
                        <table id="dueRemindersListTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Sponsor</th>
                                    <th>Month</th>
                                    <th>Days Ago</th>
                                    <th>Max Days</th>
                                    <th>1st Reminder</th>
                                    <th>2nd Reminder</th>
                                    <th>Final Reminder</th>
                                    <th>Paid</th>
                                    <th>Comment</th>
                                    <th>Set By</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="text-start mb-4">
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="waitingBtn" data-bs-target="#waitingListOffcanvas2" aria-controls="waitingListOffcanvas2">
                <i class="bi bi-list-check"></i>
                Waiting List
            </button>
            <button class="btn btn-primary position-relative" type="button" data-bs-toggle="offcanvas" id="hmoApprovalListBtn"
                data-bs-target="#hmoApprovalListOffcanvas" aria-controls="hmoApprovalListOffcanvas">
                <i class="bi bi-list-check"></i>
                PHIS Approval List <span class="badge text-bg-danger" id="hmoApprovalListCount"></span>
            </button>
            <button class="btn btn-primary position-relative" type="button" data-bs-toggle="offcanvas" id="nhisApprovalListBtn"
                data-bs-target="#nhisApprovalListOffcanvas" aria-controls="nhisApprovalListOffcanvas">
                <i class="bi bi-list-check"></i>
                NHIS Approval List <span class="badge text-bg-danger" id="nhisApprovalListCount"></span>
            </button>
            <button class="btn btn-primary position-relative" type="button" data-bs-toggle="offcanvas" id="dueRemindersListBtn"
                data-bs-target="#dueRemindersListOffcanvas" aria-controls="dueRemindersListOffcanvas">
                <i class="bi bi-list-check"></i>
                Due Reminders <span class="badge text-bg-danger" id="dueRemindersListCount"></span>
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

                    <button class="nav-link" id="nav-sentBills-tab" data-bs-toggle="tab" data-bs-target="#nav-sentBills"
                        type="button" role="tab" aria-controls="nav-sentBills" aria-selected="false">Sent Bills</button>

                    <button class="nav-link" id="nav-hmoReports-tab" data-bs-toggle="tab" data-bs-target="#nav-hmoReports"
                        type="button" role="tab" aria-controls="nav-hmoReports" aria-selected="false">Reports</button>

                    <button class="nav-link" id="nav-nhisRecon-tab" data-bs-toggle="tab" data-bs-target="#nav-nhisRecon"
                        type="button" role="tab" aria-controls="nav-nhisRecon" aria-selected="false">NHIS Capitation</button>

                    <button class="nav-link" id="nav-billReminders-tab" data-bs-toggle="tab" data-bs-target="#nav-billReminders"
                        type="button" role="tab" aria-controls="nav-billReminders" aria-selected="false">Bill Reminders</button>
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
                                    <th>Last 30days</th>
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
                        <table id="hmoTreatmentsTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Last 30days</th>
                                    <th>Personnel</th>
                                    <th>LabTest</th>
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
                <div class="tab-pane fade" id="nav-sentBills" role="tabpanel" aria-labelledby="nav-sentBills-tab" tabindex="0">
                    <x-form-div class="col-xl-8 pt-3 datesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="monthYear" id="monthYear" />
                        <button class="input-group-text searchBillsMonthBtn">Search</button>
                    </x-form-div>
                    <div class="pt-3 ">
                        <table id="sentBillsTable" class="table table-hover align-middle table-sm sentBillsTable">
                            <thead>
                                <tr>
                                    <th>Came</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Sent By</th>
                                    <th>Total HmsBill</th>
                                    <th>Total HmoBill</th>
                                    <th><span class="removeFilter">Details/</span><span class="filterByOpen">State</span></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="">
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- reports table -->
                <div class="tab-pane fade" id="nav-hmoReports" role="tabpanel" aria-labelledby="nav-hmoReports-tab"
                    tabindex="0">
                    <x-form-div class="col-xl-10 pt-2 reportsDatesDiv">
                        <x-input-span id="filterListLabel">Category List<x-required-span /></x-input-span>
                        <select class="form-select form-select-md" name="category" id="category">
                            <option value="">All</option>
                            <option value="HMO">HMO</option>
                            <option value="NHIS">NHIS</option>
                            <option value="Retainership">Retainership</option>
                            <option value="Compare">Compare</option>
                        </select>
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchReportsBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="monthYear" id="monthYear" />
                        <button class="input-group-text searchReportsMonthBtn">Search</button>
                    </x-form-div>
                    <div class="py-2 justify-content-center">
                        <table id="hmoReportsTable" class="table table-sm hmoReportsTable">
                            <thead>
                                <tr>
                                    <th>Sponsor</th>
                                    <th>Mon/Year</th>
                                    <th>Visits</th>
                                    <th>Bills Sent</th>
                                    <th>HMO Bill</th>
                                    <th>HMS Bill</th>
                                    <th>Bill Diff</th>
                                    <th>Total Paid</th>
                                    <th>Total Cap</th>
                                    <th>Paid - HMS</th>
                                    <th>Paid - HMO</th>
                                    <th>HMS Debt%</th>
                                    <th>HMO Debt%</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="">
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
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
                <!-- nhis capitation reconciliation table -->
                <div class="tab-pane fade" id="nav-nhisRecon" role="tabpanel" aria-labelledby="nav-nhisRecon-tab"
                    tabindex="0">
                    <x-form-div class="col-xl-4 pt-2 nhisMonthYearDiv">
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="nhisDate" id="nhisDate" />
                        <button class="input-group-text searchNhisConBtn">Search</button>
                    </x-form-div>
                    <div class="py-2 justify-content-center">
                        <table id="nhisReconTable" class="table table-sm nhisReconTable">
                            <thead>
                                <tr>
                                    <th>Sponsor</th>
                                    <th>Px</th>
                                    <th>Px Visits</th>
                                    <th>Visits</th>
                                    <th>Visits Rx</th>
                                    <th>Rx</th>
                                    <th>Hms Bill</th>
                                    <th>Nhis Bill</th>
                                    <th>Paid</th>
                                    <th>Capitation</th>
                                    <th>Gross</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="">
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
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
                <!-- Hmo Bill reminders tracker table -->
                <div class="tab-pane fade" id="nav-billReminders" role="tabpanel" aria-labelledby="nav-billReminders-tab"
                    tabindex="0">
                    <x-form-div class="col-xl-8 pt-3 billRemindersDatesDiv">
                        <x-input-span class="">Start</x-input-span>
                        <x-form-input type="date" name="startDate" id="startDate" />
                        <x-input-span class="">End</x-input-span>
                        <x-form-input type="date" name="endDate" id="endDate" />
                        <button class="input-group-text searchBillRemindersWithDatesBtn">Search</button>
                        <x-input-span class="">OR</x-input-span>
                        <x-input-span class="">Month/Year</x-input-span>
                        <x-form-input type="month" name="monthYear" id="monthYear" />
                        <button class="input-group-text searchBillRemindersMonthBtn">Search</button>
                    </x-form-div>
                    <div class="py-2 justify-content-center">
                        <table id="billRemindersTable" class="table table-sm billRemindersTable">
                            <thead>
                                <tr>
                                    <th>Sponsor</th>
                                    <th>Month</th>
                                    <th>Sent On</th>
                                    <th>Days Ago</th>
                                    <th>Max Days</th>
                                    <th>Paid After</th>
                                    <th>1st Reminder</th>
                                    <th>Date</th>
                                    <th>2nd Reminder</th>
                                    <th>Date</th>
                                    <th>Final Reminder</th>
                                    <th>Date</th>
                                    <th>Remind?</th>
                                    <th>Paid</th>
                                    <th>Created At</th>
                                    <th>Set By</th>
                                    <th>Comment</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
@endsection
