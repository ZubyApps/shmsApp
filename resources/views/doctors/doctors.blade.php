@extends('layout')

@section('content')
@vite(['resources/css/colourblink.scss', 'resources/js/doctors.js'])

@include('doctors.consultationModal', ['title' => 'New Consultation', 'isSpecialist' => false, 'isNurse' => false, 'id' => 'newConsultationModal'])
@include('doctors.consultationModal', ['title' => 'New Specialist Consultation', 'isSpecialist' => true, 'isNurse' => false, 'id' => 'specialistConsultationModal'])

@include('doctors.ancConsultationModal', ['title' => 'New ANC Consultation', 'isReview' => false, 'isNurse' => false, 'id' => 'ancConsultationModal'])
@include('doctors.ancConsultationModal', ['title' => 'New ANC Review', 'isReview' => true, 'isNurse' => false, 'id' => 'ancReviewModal'])

@include('doctors.consultationReviewModal', ['title' => 'Consultation Review', 'isAnc' => false, 'id' => 'consultationReviewModal'])
@include('doctors.consultationReviewModal', ['title' => 'ANC Consultation Review', 'isAnc' => true, 'isNurse' => false, 'id' => 'ancConsultationReviewModal'])
@include('doctors.newReviewModal', ['title' => 'New Review', 'isUpdate' => false, 'isNurse' => false, 'id' => 'newReviewModal'])
@include('doctors.consultationHistoryModal', ['title' => 'Consultation History', 'isAnc' => false, 'id' => 'consultationHistoryModal'])
@include('vitalsigns.vitalsignsModal', ['title' => 'Vital Signs', 'isDoctor' => true, 'id' => 'vitalsignsModal'])
@include('vitalsigns.ancVitalsignsModal', ['title' => 'Anc Vital Signs', 'isDoctor' => true, 'id' => 'ancVitalsignsModal', ])
@include('nurses.prescriptionsModal', ['title' => 'Medications for this Visit', 'isMedications' => true, 'isDoctor' => true, 'id' => 'medicationPrescriptionsModal'])
@include('nurses.prescriptionsModal', ['title' => 'Other prescriptions for this Visit', 'isMedications' => false, 'isDoctor' => false, 'id' => 'otherPrescriptionsModal'])
@include('investigations.investigationsModal', ['title' => 'Investigations', 'isDoctor' => true, 'id' => 'investigationsModal'])
@include('investigations.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])
@include('investigations.addResultModal', ['title' => 'Update Result', 'isUpdate' => true, 'id' => 'updateResultModal'])
@include('extras.investigationAndManagementModal', ['title' => 'Update Investigation and Management', 'isNurse' => false, 'id' => 'investigationAndManagementModal'])
@include('doctors.dischargeModal', ['title' => 'Discharge Patient', 'isNurses' => false, 'id' => 'dischargeModal'])
@include('doctors.surgeryModal', ['title' => 'New Surgery', 'isUpdate' => false, 'isView' => false, 'id' => 'newSurgeryModal'])
@include('doctors.surgeryModal', ['title' => 'Update Surgery', 'isUpdate' => true, 'isView' => false, 'id' => 'updateSurgeryModal'])
@include('doctors.surgeryModal', ['title' => 'View Surgery', 'isUpdate' => false, 'isView' => true, 'id' => 'viewSurgeryModal'])
@include('doctors.fileModal', ['title' => 'Upload Docs', 'isUpdate' => false, 'id' => 'fileModal'])
@include('extras.medicalReportListModal', ['title' => 'Medical Report List', 'isDoctor' => true, 'id' => 'medicalReportListModal' ])
@include('extras.medicalReportTemplateModal', ['title' => 'New Medical Report', 'isUpdate' => false, 'id' => 'newMedicalReportTemplateModal' ])
@include('extras.medicalReportTemplateModal', ['title' => 'Edit Medical Report', 'isUpdate' => true, 'id' => 'editMedicalReportTemplateModal' ])
@include('extras.viewMedicalReportModal', ['title' => '', 'isUpdate' => true, 'id' => 'viewMedicalReportModal' ])
@include('nurses.wardAndBedModal', ['title' => 'Update Admission Details', 'isNurses' => false, 'id' => 'wardAndBedModal'])
@include('doctors.appointmentModal', ['title' => 'Set Appointment', 'isDoctor' => true, 'id' => 'appointmentModal'])
@include('doctors.procedureBookingModal', ['title' => 'Set Operation/Procedure Date & Details', 'isDoctor' => true, 'id' => 'procedureBookingModal'])
@include('nurses.labourRecordModal', ['title' => 'New Labour Record', 'isUpdate' => false, 'dept' => 'doctors', 'isView' => false, 'id' => 'newLabourRecordModal'])
@include('nurses.labourRecordModal', ['title' => 'Update Labour Record', 'isUpdate' => true, 'dept' => 'doctors', 'isView' => false, 'id' => 'updateLabourRecordModal'])
@include('nurses.labourRecordModal', ['title' => 'View Labour Record', 'isUpdate' => false, 'dept' => 'doctors', 'isView' => true, 'id' => 'viewLabourRecordModal'])
@include('nurses.summaryOfLabourModal', ['title' => 'Save Labour Summary', 'isUpdate' => true, 'dept' => 'doctors', 'isView' => false, 'id' => 'saveLabourSummaryModal'])
@include('nurses.summaryOfLabourModal', ['title' => 'View Labour Summary', 'isUpdate' => false, 'dept' => 'doctors', 'isView' => true, 'id' => 'viewLabourSummaryModal'])
@include('nurses.partographModal', ['title' => 'Partograph', 'isDoctor' => true, 'id' => 'partographModal', ])

    <div class="container mt-5">
        <input type="text" class="d-none" value="{{ $feverBenchMark }}" id="feverBenchMark">
        <div class="offcanvas offcanvas-start overflow-auto" data-bs-scroll="true" tabindex="-1" id="waitingListOffcanvas1"
            aria-labelledby="waitingListOffcanvasLabel">
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
                                <th>Vitals</th>
                                <th>Emerg</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
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
        <div class="offcanvas offcanvas-end overflow-auto" data-bs-scroll="true" tabindex="-1" id="appointmentsOffcanvas"
            aria-labelledby="appointmentsOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="appointmentsOffcanvasLabel">List of Appointments</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <x-form-div class="col-md-6 pt-2">
                    <x-input-span id="filterAppointmentsLabel">Filter Appointments<x-required-span /></x-input-span>
                    <select class="form-select form-select-md" name="filterAppointments" id="filterAppointments">
                        <option value="My Appointments">My Appointments </option>
                        <option value="">All Appointments</option>
                    </select>
                </x-form-div>
                <div class="py-4 ">
                    <table id="appointmentsTable" class="table table-sm">
                        <thead>
                            <tr>
                                <th>Created</th>
                                <th>Patient</th>
                                <th><i class="bi bi-telephone-outbound-fill text-primary"></th>
                                <th>Sponsor</th>
                                <th>Last Visit</th>
                                <th>Last Diagnosis</th>
                                <th>Doctor</th>
                                <th>Ap Date</th>
                                <th>CreatedBy</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-end overflow-auto" data-bs-scroll="true" tabindex="-1" id="proceduresListOffcanvas"
            aria-labelledby="proceduresListOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="proceduresListOffcanvasLabel">Procedures List</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="py-4 ">
                    <table id="proceduresListTable" class="table table-hover table-sm proceduresListTable">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Patient</th>
                                <th>Phone</th>
                                <th>By</th>
                                <th>Sponsor</th>
                                <th>Procedure</th>
                                <th>Booked</th>
                                <th>Booked By</th>
                                <th>Comment</th>
                                <th>Status</th>
                                <th>Status By</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-start mb-2">
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" id="waitingBtn"
                data-bs-target="#waitingListOffcanvas1" aria-controls="waitingListOffcanvas">
                <i class="bi bi-list-check"></i>
                Waiting List
            </button>
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" id="emergencyListBtn"
                data-bs-target="#emergencyListOffcanvas" aria-controls="emergencyListOffcanvas">
                <i class="bi bi-list-check"></i>
                Emergency Rx <span class="badge text-bg-danger" id="emergencyListCount"></span>
            </button>
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" id="appointmentsListBtn"
                data-bs-target="#appointmentsOffcanvas" aria-controls="appointmentsOffcanvas">
                <i class="bi bi-list-check"></i>
                Appointments <span class="badge text-bg-danger" id="appointmentsBadgeSpan"></span>
            </button>
            <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas" id="proceduresListBtn"
                data-bs-target="#proceduresListOffcanvas" aria-controls="proceduresListOffcanvas">
                <i class="bi bi-list-check"></i>
                Procedures List <span class="badge text-bg-danger" id="proceduresListCount"></span>
            </button>
        </div>
        <div class="text-end mb-2" id="labourInProgressDiv"></div>

        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    
                    <button class="nav-link active" id="nav-outPatients-tab"  data-bs-toggle="tab"  data-bs-target="#nav-outPatients-view"
                    type="button" role="tab" aria-controls="nav-outPatients" aria-selected="false">OutPatients</button>

                    <button class="nav-link" id="nav-inPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-inPatients-view" 
                    type="button" role="tab" aria-controls="nav-inPatients"  aria-selected="true">Inpatients</button>

                    <button class="nav-link" id="nav-ancPatients-tab"  data-bs-toggle="tab"  data-bs-target="#nav-ancPatients-view"
                    type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>

                    {{-- <button class="nav-link" id="nav-appointments-tab"  data-bs-toggle="tab"  data-bs-target="#nav-appointments"
                    type="button" role="tab" aria-controls="nav-appointments" aria-selected="false">Appointments</button> --}}

                    <button class="nav-link" id="nav-procedures-tab" data-bs-toggle="tab" data-bs-target="#nav-procedures-view"
                    type="button" role="tab" aria-controls="nav-procedures" aria-selected="false">Procedures</button>
                    
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- your regular patients table -->
                <div class="tab-pane fade show active"  id="nav-outPatients-view" aria-labelledby="nav-outPatients-tab" role="tabpanel"  tabindex="0">
                    <x-form-div class="col-md-4 pt-2">
                        <x-input-span id="filterListLabel">Filter List<x-required-span /></x-input-span>
                        <select class="form-select form-select-md" name="filterList" id="filterListOutPatients">
                            <option value="My Patients">My Patients </option>
                            <option value="">All Patients</option>
                        </select>
                    </x-form-div>
                    <div class="py-4">
                        <table id="outPatientsVisitTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Last Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Last 30days</th>
                                    <th>Rx Count</th>
                                    <th>Other Rx</th>
                                    <th>Lab Count</th>
                                    <th>Vitals</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>

                <!--all regular patients table -->
                <div class="tab-pane fade" id="nav-inPatients-view" aria-labelledby="nav-inPatients-tab" role="tabpanel"   tabindex="0">
                    <x-form-div class="col-md-4 pt-2">
                        <x-input-span id="filterListLabel">Filter List<x-required-span /></x-input-span>
                        <select class="form-select form-select-md" name="filterList" id="filterListInPatients">
                            <option value="My Patients">My Patients </option>
                            <option value="">All Patients</option>
                        </select>
                    </x-form-div>
                    <div class="py-4">
                        <table id="inPatientsVisitTable"  class="table align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Last 30days</th>
                                    <th>Rx Count</th>
                                    <th>Other Rx</th>
                                    <th>Lab Count</th>
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

                <!-- your anc patients table -->
                <div class="tab-pane fade"  id="nav-ancPatients-view" aria-labelledby="nav-ancPatients-tab" role="tabpanel"  tabindex="0">
                    <x-form-div class="col-md-4 pt-2">
                        <x-input-span id="filterListLabel">Filter List<x-required-span /></x-input-span>
                        <select class="form-select form-select-md" name="filterList" id="filterListAncPatients">
                            <option value="My Patients">My Patients </option>
                            <option value="">All Patients</option>
                        </select>
                    </x-form-div>
                    <div class="py-4">
                        <table id="ancPatientsVisitTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>ANC</th>
                                    <th>Rx Count</th>
                                    <th>Other Rx</th>
                                    <th>Lab Count</th>
                                    <th>Vitals</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- procedures table -->
                <div class="tab-pane fade" id="nav-procedures-view" role="tabpanel" aria-labelledby="nav-procedures-tab" tabindex="0">
                    <div class="py-4 ">
                        <table id="proceduresTable" class="table table-hover align-middle table-sm proceduresTable">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Phone</th>
                                    <th>Prescribed by</th>
                                    <th>Sponsor</th>
                                    <th>Procedure</th>
                                    <th>Booked</th>
                                    <th>Comment</th>
                                    <th>Booked By</th>
                                    <th>Status</th>
                                    <th>Status By</th>
                                    <th>Action</th>
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
