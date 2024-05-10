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
@include('nurses.wardAndBedModal', ['title' => 'Update Admission Details', 'isNurses' => true, 'id' => 'wardAndBedModal'])

    <div class="container mt-5">
        <div class="offcanvas offcanvas-start overflow-auto" data-bs-scroll="true" tabindex="-1" id="waitingListOffcanvas1"
            aria-labelledby="waitingListOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="waitingListOffcanvasLabel">List of Waiting Patients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <x-form-div class="col-xl-10 py-3 payMethodDiv">
                    <x-input-span class="">Average Waiting Time</x-input-span>
                    <x-input-span class="">Last Week</x-input-span>
                    <x-form-input name="lastWeek" id="lastWeek" readonly/>
                    <x-input-span class="">This Week</x-input-span>
                    <x-form-input name="thisWeek" id="thisWeek" readonly/>
                </x-form-div>
                <div class="py-4 ">
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

        <div class="text-start mb-4">
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
        </div>

        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    
                    <button class="nav-link active" id="nav-outPatients-tab"  data-bs-toggle="tab"  data-bs-target="#nav-outPatients"
                    type="button" role="tab" aria-controls="nav-outPatients" aria-selected="false">OutPatients</button>

                    <button class="nav-link" id="nav-inPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-inPatients" 
                    type="button" role="tab" aria-controls="nav-inPatients"  aria-selected="true">Inpatients</button>

                    <button class="nav-link" id="nav-ancPatients-tab"  data-bs-toggle="tab"  data-bs-target="#nav-ancPatients"
                    type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>
                    
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- your regular patients table -->
                <div class="tab-pane fade show active"  id="nav-outPatients" aria-labelledby="nav-outPatients-tab" role="tabpanel"  tabindex="0">
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
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Last 30days</th>
                                    <th>Rx Count</th>
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
                <div class="tab-pane fade" id="nav-inPatients" aria-labelledby="nav-inPatients-tab" role="tabpanel"   tabindex="0">
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
                <div class="tab-pane fade"  id="nav-ancPatients" aria-labelledby="nav-ancPatients-tab" role="tabpanel"  tabindex="0">
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
            </div>
        </div>
    </div>
@endsection
