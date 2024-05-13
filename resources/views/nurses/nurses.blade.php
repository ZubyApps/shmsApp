@extends('layout')

@section('content')
@vite(['resources/css/colourblink.scss', 'resources/js/nurses.js'])

@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isAnc' => false, 'isLab' => false, 'isHmo' => false, 'id' => 'treatmentDetailsModal'])
@include('doctors.consultationHistoryModal', ['title' => 'Consultation History', 'isAnc' => false, 'id' => 'consultationHistoryModal'])
@include('nurses.treatmentDetailsModal', ['title' => 'ANC Treatment Details', 'isAnc' => true, 'isLab' => false, 'isHmo' => false, 'id' => 'ancTreatmentDetailsModal'])
@include('nurses.deliveryNotesModal', ['title' => 'New Delivery Note', 'isUpdate' => false,'isView' => false, 'id' => 'newDeliveryNoteModal'])
@include('nurses.deliveryNotesModal', ['title' => 'Update Delivery Note', 'isUpdate' => true, 'isView' => false, 'id' => 'updateDeliveryNoteModal'])
@include('nurses.deliveryNotesModal', ['title' => 'View Delivery Note', 'isUpdate' => true, 'isView' => true, 'id' => 'viewDeliveryNoteModal'])
@include('nurses.prescriptionsModal', ['title' => 'Medications for this Visit', 'isMedications' => true, 'isDoctor' => false, 'id' => 'medicationPrescriptionsModal'])
@include('nurses.prescriptionsModal', ['title' => 'Other prescriptions for this Visit', 'isMedications' => false, 'isDoctor' => false, 'id' => 'otherPrescriptionsModal'])
@include('nurses.chartPrescriptionModal', ['title' => 'Chart Prescription', 'isUpdate' => false, 'id' => 'chartPrescriptionModal'])
@include('nurses.chartMedicationModal', ['title' => 'Chart Medication', 'isUpdate' => false, 'id' => 'chartMedicationModal'])
@include('nurses.ancRegisterationModal', ['title' => 'New ANC Registeration', 'isUpdate' => false, 'isView' => false, 'id' => 'newAncRegisterationModal', ])
@include('nurses.ancRegisterationModal', ['title' => 'Update ANC Registeration', 'isUpdate' => true, 'isView' => false, 'id' => 'updateAncRegisterationModal', ])
@include('nurses.ancRegisterationModal', ['title' => 'View ANC Registeration', 'isUpdate' => false, 'isView' => true, 'id' => 'viewAncRegisterationModal', ])
@include('vitalsigns.vitalsignsModal', ['title' => 'Vital Signs', 'isDoctor' => false, 'id' => 'vitalsignsModal', ])
@include('vitalsigns.ancVitalsignsModal', ['title' => 'Anc Vital Signs', 'isDoctor' => false, 'id' => 'ancVitalsignsModal', ])
@include('nurses.giveMedicationModal')
@include('nurses.serviceDoneModal')
@include('doctors.dischargeModal', ['title' => 'Discharge Patient', 'isNurses' => false, 'id' => 'dischargeModal'])
@include('extras.bulkRequestModal', ['title' => 'Bulk Request', 'dept' => 'Nurses', 'isPharmacy' => false, 'id' => 'bulkRequestModal'])
@include('extras.investigationAndManagementModal', ['title' => 'Emergency Management', 'isNurse' => true, 'id' => 'investigationAndManagementModal'])
@include('extras.nursesReportModal', ['title' => 'Nurses Report', 'isNurses' => true, 'id' => 'nursesReportModal'])
@include('extras.nursesReportTemplateModal', ['title' => 'New Report', 'isUpdate' => false, 'id' => 'newNursesReportTemplateModal' ])
@include('extras.nursesReportTemplateModal', ['title' => 'Edit Report', 'isUpdate' => true, 'id' => 'editNursesReportTemplateModal' ])
@include('nurses.wardAndBedModal', ['title' => 'Update Admission Details', 'isNurses' => true, 'id' => 'wardAndBedModal'])
@include('doctors.fileModal', ['title' => 'Upload Docs', 'isUpdate' => false, 'id' => 'fileModal'])

<div class="container mt-5">
    <div class="offcanvas offcanvas-top overflow-auto" data-bs-scroll="true" tabindex="-1" id="upcomingMedicationsoffcanvas"
        aria-labelledby="upcomingMedicationsoffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary" id="upcomingMedicationsoffcanvasLabel">List of Upcoming Inpatients Medication</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="py-4 ">
                <table id="upcomingMedicationsTable" class="table align-middle table-sm">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Status</th>
                            <th>Ward</th>
                            <th>Treatment</th>
                            <th>Prescription</th>
                            <th>Dose</th>
                            <th>Charted By</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Give</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-bottom overflow-auto" data-bs-scroll="true" tabindex="-1" id="upcomingNursingChartsoffcanvas"
        aria-labelledby="upcomingNursingChartsoffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary" id="upcomingNursingChartsoffcanvasLabel">List of Upcoming Inpatients Care Schedule</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="py-4 ">
                <table id="upcomingNursingChartsTable" class="table table-sm">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Status</th>
                            <th>Ward</th>
                            <th>Treatment</th>
                            <th>Instruction</th>
                            <th>Charted By</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Record</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
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
                <table id="waitingTable" class="table table-hover table-sm">
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
                            <th>Emerg</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>

        <div class="text-start mb-4">
           
        </div>
    <div class="text-start mb-4">
        <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="waitingBtn" data-bs-target="#waitingListOffcanvas2" aria-controls="waitingListOffcanvas2">
            <i class="bi bi-list-check"></i>
            Waiting List
        </button>
        <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="inpatientsMedChartBtn" data-bs-target="#upcomingMedicationsoffcanvas" aria-controls="upcomingMedicationsoffcanvas">
            <i class="bi bi-list-check"></i>
            Inpatients Medication Chart <span class="badge text-bg-danger" id="inpatientMedicationBadgeSpan"></span>
        </button>
        <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="nursingChartBtn" data-bs-target="#upcomingNursingChartsoffcanvas" aria-controls="upcomingNursingChartsoffcanvas">
            <i class="bi bi-list-check"></i>
            Inpatients Nursing Chart <span class="badge text-bg-danger" id="inpatientNursingBadgeSpan"></span>
        </button>
    </div>

    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-inPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-inPatients"
                    type="button" role="tab" aria-controls="nav-inPatients" aria-selected="false">Inpatients</button>

                <button class="nav-link" id="nav-outPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-outPatients" 
                    type="button" role="tab" aria-controls="nav-outPatients" aria-selected="true">OutPatients</button>


                <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients"
                    type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>

                <button class="nav-link" id="nav-bulkRequests-tab" data-bs-toggle="tab" data-bs-target="#nav-bulkRequests"
                    type="button" role="tab" aria-controls="nav-bulkRequests" aria-selected="false">Bulk Requests</button>

                <button class="nav-link" id="nav-emergency-tab" data-bs-toggle="tab" data-bs-target="#nav-emergency"
                    type="button" role="tab" aria-controls="nav-emergency" aria-selected="false">Emergency</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- inpatients table -->
            <div class="tab-pane fade show active" id="nav-inPatients" role="tabpanel" aria-labelledby="nav-inPatients-tab"
                tabindex="0">
                <div class="py-4 ">
                    <table id="inPatientsVisitTable" class="table table-hover align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Seen</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Current Diagnosis</th>
                                <th>Sponsor</th>
                                <th>Rx count</th>
                                <th>AdditionalRx</th>
                                <th>Vitals</th>
                                <th>Status</th>
                                <th>Ward</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
             <!-- outpatients table -->
            <div class="tab-pane fade" id="nav-outPatients" role="tabpanel" aria-labelledby="nav-outPatients-tab" tabindex="0">
                <div class="py-4">
                    <table id="outPatientsVisitTable" class="table table-hover align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Seen</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Current Diagnosis</th>
                                <th>Sponsor</th>
                                <th>Rx count</th>
                                <th>Other</th>
                                <th>Vitals</th>
                                <th>Status</th>
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
                    <table id="ancPatientsVisitTable" class="table table-hover align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Seen</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Current Diagnosis</th>
                                <th>Sponsor</th>
                                <th>Rx count</th>
                                <th>Other/Register</th>
                                <th>Vitals</th>
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
                    <table id="bulkRequestsTable" class="table table-hover align-middle table-sm bulkRequestsTable">
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
                                <th>Medication/Item</th>
                                <th>Prescription</th>
                                <th>Qty</th>
                                <th>Prescribed By</th>
                                <th>Note</th>
                                <th>DOC</th>
                                <th>Chart</th>
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
