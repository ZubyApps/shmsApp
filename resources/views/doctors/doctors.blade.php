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
                    <button class="nav-link active" id="nav-allPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-allPatients" 
                    type="button" role="tab" aria-controls="nav-allPatients"  aria-selected="true">All Patients Consultations</button>

                    <button class="nav-link" id="nav-yourPatients-tab"  data-bs-toggle="tab"  data-bs-target="#nav-yourPatients"
                        type="button" role="tab" aria-controls="nav-yourPatients" aria-selected="false">Your Patients Consultations</button>

                    {{-- <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                        type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Sponsors</button> --}}
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-allPatients" aria-labelledby="nav-allPatients-tab" role="tabpanel"  tabindex="0">
                    <div class="py-4">
                        <table id="allPatientsVisitTable"  class="table table-hover align-middle table-sm">
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
                <!-- active table -->
                <div class="tab-pane fade" id="nav-yourPatients" role="tabpanel" aria-labelledby="nav-yourPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="yourPatientsVisitTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- sponsors table -->
                {{-- <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <table id="sponsorsTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Sponsor Name</th>
                                    <th>Payment Category</th>
                                    <th>Sponsor Category</th>
                                    <th>Payment Matrix</th>
                                    <th>Balance Required?</th>
                                    <th>Registration Bill</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
