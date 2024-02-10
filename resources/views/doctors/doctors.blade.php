@extends('layout')

@section('content')
@vite(['resources/js/doctors.js'])

@include('doctors.consultationModal', ['title' => 'New Consultation', 'isSpecialist' => false, 'id' => 'newConsultationModal'])
@include('doctors.consultationModal', ['title' => 'New Specialist Consultation', 'isSpecialist' => true, 'id' => 'specialistConsultationModal'])

@include('doctors.ancConsultationModal', ['title' => 'New ANC Consultation', 'isReview' => false, 'id' => 'ancConsultationModal'])
@include('doctors.ancConsultationModal', ['title' => 'New ANC Review', 'isReview' => true, 'id' => 'ancReviewModal'])

@include('doctors.consultationReviewModal', ['title' => 'Consultation Review', 'isAnc' => false, 'id' => 'consultationReviewModal'])
@include('doctors.consultationHistoryModal', ['title' => 'Consultation History', 'isAnc' => false, 'id' => 'consultationHistoryModal'])
@include('doctors.consultationReviewModal', ['title' => 'ANC Consultation Review', 'isAnc' => true, 'id' => 'ancConsultationReviewModal'])
@include('vitalsigns.vitalsignsModal', ['title' => 'Vital Signs', 'isDoctor' => true, 'id' => 'vitalsignsModal'])
@include('vitalsigns.ancVitalsignsModal', ['title' => 'Anc Vital Signs', 'isDoctor' => true, 'id' => 'ancVitalsignsModal', ])
@include('investigations.investigationsModal', ['title' => 'Investigations', 'isDoctor' => true, 'id' => 'investigationsModal'])
@include('investigations.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])
@include('investigations.addResultModal', ['title' => 'Update Result', 'isUpdate' => true, 'id' => 'updateResultModal'])
@include('extras.investigationAndManagementModal', ['title' => 'Update Investigation and Management', 'id' => 'investigationAndManagementModal'])
@include('doctors.dischargeModal', ['title' => 'Discharge Patient', 'isNurses' => false, 'id' => 'dischargeModal'])
@include('doctors.surgeryModal', ['title' => 'New Surgery', 'isUpdate' => false, 'isView' => false, 'id' => 'newSurgeryModal'])
@include('doctors.surgeryModal', ['title' => 'Update Surgery', 'isUpdate' => true, 'isView' => false, 'id' => 'updateSurgeryModal'])
@include('doctors.surgeryModal', ['title' => 'View Surgery', 'isUpdate' => false, 'isView' => true, 'id' => 'viewSurgeryModal'])
@include('doctors.fileModal', ['title' => 'Upload Docs', 'isUpdate' => false, 'id' => 'fileModal'])
@include('doctors.newReviewModal', ['title' => 'New Review', 'isUpdate' => false, 'id' => 'newReviewModal'])

    <div class="container p-1 mt-5">
        <div class="offcanvas offcanvas-start overflow-auto" data-bs-scroll="true" tabindex="-1" id="waitingListOffcanvas1"
            aria-labelledby="waitingListOffcanvasLabel">
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
                                <th>Vitals</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-start mb-4">
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="waitingBtn"
                data-bs-target="#waitingListOffcanvas1" aria-controls="waitingListOffcanvas">
                <i class="bi bi-list-check"></i>
                Waiting List
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
                                    <th>Investigations</th>
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
                                    <th>Investigations</th>
                                    <th>Vitals</th>
                                    <th>Status</th>
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
                                    <th>Investigations</th>
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
