@extends('layout')

@section('content')
@vite(['resources/js/nurses.js'])

@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isLab' => false, 'id' => 'treatmentDetailsModal'])
@include('nurses.deliveryNotesModal', ['title' => 'New Delivery Note', 'isUpdate' => false, 'id' => 'newDeliveryNoteModal'])
@include('nurses.deliveryNotesModal', ['title' => 'Update Delivery Note', 'isUpdate' => true, 'id' => 'updateDeliveryNoteModal'])
@include('nurses.chartMedicationModal', ['title' => 'Chart Medication', 'isUpdate' => false, 'id' => 'chartMedicationModal'])
@include('vitalsigns.vitalsignsModal', ['title' => 'Vital Signs', 'isDoctor' => false, 'id' => 'vitalsignsModal', ])
@include('nurses.giveMedicationModal')

<div class="container p-1 mt-5">
    <div class="offcanvas offcanvas-top" data-bs-scroll="true" tabindex="-1" id="upcomingMedicationsoffcanvas"
        aria-labelledby="upcomingMedicationsoffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary" id="upcomingMedicationsoffcanvasLabel">List of Upcoming Medications</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="py-4 ">
                <table id="upcomingMedicationsTable" class="table table-hover align-middle table-sm bg-primary">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Status</th>
                            <th>Ward</th>
                            <th>Treatment</th>
                            <th>Prescription</th>
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
    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="waitingListOffcanvas2"
            aria-labelledby="waitingListOffcanvasLabel" aria-expanded="false">
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
                                <th>Dr</th>
                                <th>Vitals</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        {{-- @include('visits.waitingList') --}}

        <div class="text-start mb-4">
           
        </div>
    <div class="text-start mb-4">
        <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
            data-bs-target="#upcomingMedicationsoffcanvas" aria-controls="upcomingMedicationsoffcanvas">
            <i class="bi bi-list-check"></i>
            Medication Table
        </button>
        <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="waitingBtn"
        data-bs-target="#waitingListOffcanvas2" aria-controls="waitingListOffcanvas2">
        <i class="bi bi-list-check"></i>
        Waiting List
    </button>
    </div>

    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-allRegularPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-allRegularPatients" 
                    type="button" role="tab" aria-controls="nav-allRegularPatients" aria-selected="true">All Regular Patients</button>

                <button class="nav-link" id="nav-inPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-inPatients"
                    type="button" role="tab" aria-controls="nav-inPatients" aria-selected="false">Inpatients</button>

                <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients"
                    type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- patients table -->
            <div class="tab-pane fade show active" id="nav-allRegularPatients" role="tabpanel"
                aria-labelledby="nav-allRegularPatients-tab" tabindex="0">
                <div class="py-4">
                    <table id="allRegularPatientsTable" class="table table-hover align-middle table-sm">
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
            <!-- inpatients table -->
            <div class="tab-pane fade" id="nav-inPatients" role="tabpanel" aria-labelledby="nav-inPatients-tab"
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
