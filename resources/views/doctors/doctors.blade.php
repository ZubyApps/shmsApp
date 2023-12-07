@extends('layout')

@section('content')
@vite(['resources/js/doctors.js'])

@include('doctors.consultationModal', ['title' => 'New Consultation', 'isSpecialist' => false, 'id' => 'newConsultationModal'])
@include('doctors.consultationModal', ['title' => 'New Specialist Consultation', 'isSpecialist' => true, 'id' => 'specialistConsultationModal'])

@include('doctors.ancConsultationModal', ['title' => 'New ANC Consultation', 'isReview' => false, 'id' => 'ancConsultationModal'])
@include('doctors.ancConsultationModal', ['title' => 'New ANC Review', 'isReview' => true, 'id' => 'ancReviewModal'])

@include('doctors.consultationReviewModal', ['title' => 'Consultation Review', 'isAnc' => false, 'id' => 'consultationReviewModal'])
@include('vitalsigns.vitalsignsModal', ['title' => 'Vital Signs', 'isDoctor' => true, 'id' => 'vitalsignsModal'])

@include('doctors.surgeryModal', ['title' => 'New Surgery', 'isUpdate' => false, 'id' => 'surgeryModal'])
@include('doctors.fileModal', ['title' => 'Upload Docs', 'isUpdate' => false, 'id' => 'fileModal'])
@include('doctors.newReviewModal', ['title' => 'New Review', 'isUpdate' => false, 'id' => 'newReviewModal'])

    <div class="container p-1 mt-5">
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="waitingListOffcanvas1"
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
        {{-- @include('visits.waitingList') --}}

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
                    
                    <button class="nav-link active" id="nav-yourRegularPatients-tab"  data-bs-toggle="tab"  data-bs-target="#nav-yourRegularPatients"
                    type="button" role="tab" aria-controls="nav-yourRegularPatients" aria-selected="false">Your Regular Patients</button>

                    <button class="nav-link" id="nav-yourAncPatients-tab"  data-bs-toggle="tab"  data-bs-target="#nav-yourAncPatients"
                    type="button" role="tab" aria-controls="nav-yourAncPatients" aria-selected="false">Your ANC Patients</button>
                    
                    <button class="nav-link" id="nav-allRegularPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-allRegularPatients" 
                    type="button" role="tab" aria-controls="nav-allRegularPatients"  aria-selected="true">All Regular Patients</button>

                    <button class="nav-link" id="nav-allAncPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-allAncPatients" 
                    type="button" role="tab" aria-controls="nav-allAncPatients"  aria-selected="true">All ANC Patients</button>
                    
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- your regular patients table -->
                <div class="tab-pane fade show active"  id="nav-yourRegularPatients" aria-labelledby="nav-yourRegularPatients-tab" role="tabpanel"  tabindex="0">
                    <div class="py-4">
                        <table id="userRegularPatientsVisitTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Diagnosis</th>
                                    <th>Sponsor</th>
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
                <div class="tab-pane fade active"  id="nav-yourAncPatients" aria-labelledby="nav-yourAncPatients-tab" role="tabpanel"  tabindex="0">
                    <div class="py-4">
                        <table id="userAncPatientsVisitTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Diagnosis</th>
                                    <th>Sponsor</th>
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
                <div class="tab-pane fade active" id="nav-allRegularPatients" aria-labelledby="nav-allRegularPatients-tab" role="tabpanel"   tabindex="0">
                    <div class="py-4">
                        <table id="allRegularPatientsVisitTable"  class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
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
                <div class="tab-pane fade active" id="nav-allAncPatients" aria-labelledby="nav-allAncPatients-tab" role="tabpanel"   tabindex="0">
                    <div class="py-4">
                        <table id="allAncPatientsVisitTable"  class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Seen</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Current Diagnosis</th>
                                    <th>Sponsor</th>
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
