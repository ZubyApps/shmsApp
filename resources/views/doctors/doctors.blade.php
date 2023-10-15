@extends('layout')

@section('content')
@vite(['resources/js/doctors.js'])

@include('doctors.newConsultationModal', ['title' => 'New Consultation', 'isUpdate' => false, 'id' => 'newConsultationModal'])
@include('doctors.newAncConsultationModal', ['title' => 'New ANC Consultation', 'isUpdate' => false, 'id' => 'newAncConsultationModal'])
@include('doctors.consultationReviewModal', ['title' => 'Consultation Review', 'isUpdate' => false, 'id' => 'consultationReviewModal'])
@include('doctors.surgeryModal', ['title' => 'New Surgery', 'isUpdate' => false, 'id' => 'surgeryModal'])
@include('doctors.fileModal', ['title' => 'Upload Docs', 'isUpdate' => false, 'id' => 'fileModal'])
@include('doctors.newReviewModal', ['title' => 'New Review', 'isUpdate' => false, 'id' => 'newReviewModal'])
@include('doctors.specialistConsultationModal', ['title' => 'New Specialist Consultation', 'isUpdate' => false, 'id' => 'specialistConsultationModal'])

    <div class="container p-1 mt-5 bg-white">
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasWithBothOptionsLabel">List of Waiting Patients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="py-4 ">
                    <table id="waitingListTable" class="table table-hover align-middle table-sm bg-primary">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Age</th>
                                <th>Sponsor</th>
                                <th>Consult</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SH21/4012 Joesphine Ene Odeh</td>
                                <td>25</td>
                                <td>Alex Odeh Family</td>
                                <td><i class="btn btn-outline-none text-primary bi bi-clipboard-plus" id="newConsultationBtn" data-patientType="ANC"></i></td>
                            </tr>
                            <tr>
                                <td>SH23/7865 Patrick Abiodun Aso</td>
                                <td>32</td>
                                <td>Axe Mansard HMO</td>
                                <td><span class="badge rounded-pill text-bg-light text-secondary p-2" id="newConsultationBtn" data-patientType="Regular">Dr Toby</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-start mb-4">
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
                <i class="bi bi-list-check"></i>
                Waiting List
            </button>
        </div>

        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-yourPatients-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-yourPatients" type="button" role="tab" aria-controls="nav-yourPatients"
                        aria-selected="true">Your Patients Consultations</button>

                    <button class="nav-link" id="nav-allPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-allPatients"
                        type="button" role="tab" aria-controls="nav-allPatients" aria-selected="false">All Patients
                        Consultations</button>

                    {{-- <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                        type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Sponsors</button> --}}
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-yourPatients" role="tabpanel"
                    aria-labelledby="nav-yourPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="yourPatientsTable" class="table table-hover align-middle table-sm">
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
                            <tbody>
                                <tr>
                                    <td>20/09/23</td>
                                    <td>SH23/7865 Patrick Abiodun Aso</td>
                                    <td>QC30-Malingering</td>
                                    <td>Axe Mansard HMO</td>
                                    <td>Out-patient</td>
                                    <td><button class="btn btn-outline-primary" id="reviewConsultationBtn">Review</button></td>
                                </tr>
                                <tr>
                                    <td>21/05/22</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>QC30-Malingering</td>
                                    <td>Self</td>
                                    <td>In-patient</td>
                                    <td><button class="btn btn-outline-primary reviewConsultationBtn">Review</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- active table -->
                <div class="tab-pane fade" id="nav-allPatients" role="tabpanel" aria-labelledby="nav-allPatients-tab"
                    tabindex="0">
                    <div class="py-4">
                        <table id="allPatientsTable" class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
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
