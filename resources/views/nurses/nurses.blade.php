@extends('layout')

@section('content')
@vite(['resources/js/nurses.js'])

@include('nurses.treatmentDetailsModal', ['title' => 'Treatment Details', 'isUpdate' => false, 'id' => 'treatmentDetailsModal'])
@include('nurses.deliveryNotesModal', ['title' => 'New Delivery Note', 'isUpdate' => false, 'id' => 'newDeliveryNoteModal'])
@include('nurses.deliveryNotesModal', ['title' => 'Update Delivery Note', 'isUpdate' => true, 'id' => 'updateDeliveryNoteModal'])
@include('nurses.chartMedicationModal', ['title' => 'Chart Medication', 'isUpdate' => false, 'id' => 'chartMedicationModal'])

<div class="container p-1 mt-5">
    <div class="offcanvas offcanvas-top" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
        aria-labelledby="offcanvasWithBothOptionsLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title text-primary" id="offcanvasWithBothOptionsLabel">List of Upcoming Medications</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="py-4 ">
                <table id="medicationTable" class="table table-hover align-middle table-sm bg-primary">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Ward</th>
                            <th>Medication/Treatment</th>
                            <th>Prescription</th>
                            <th>Charted By</th>
                            <th>Time</th>
                            <th>Give</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="fw-semibold thisRow">
                            <td class="fw-semibold">SH21/4012 Joesphine Ene Odeh</td>
                            <td>PW 2</td>
                            <td>Iv Cipro</td>
                            <td>300mg 12hrly 2/7</td>
                            <td>Nurse Patience</td>
                            <td>11:45pm</td>
                            <td><button class="btn btn-outline-primary giveMedicationBtn"><i class="bi bi-clipboard-plus"></i></button></td>
                        </tr>
                        <tr class="fw-semibold table-warning">
                            <td>SH23/7865 Patrick Abiodun Aso</td>
                            <td>Mw Bed 1</td>
                            <td>Iv Flagyl</td>
                            <td>250mg 8hrly 2/7</td>
                            <td>Nurse Maureen</td>
                            <td>12:00am</td>
                            <td><button class="btn btn-outline-primary giveMedicationBtn"><i class="bi bi-clipboard-plus"></i></button></td>
                        </tr>
                        <tr>
                            <td>SH23/7865 Patrick Abiodun Aso</td>
                            <td>Mw Bed 1</td>
                            <td>Iv Flagyl</td>
                            <td>250mg 8hrly 2/7</td>
                            <td>Nurse Maureen</td>
                            <td>12:00am</td>
                            <td><button class="btn btn-outline-primary giveMedicationBtn"><i class="bi bi-clipboard-plus"></i></button></td>
                        </tr>
                        <tr>
                            <td>SH23/7865 Patrick Abiodun Aso</td>
                            <td>Mw Bed 1</td>
                            <td>Iv Flagyl</td>
                            <td>250mg 8hrly 2/7</td>
                            <td>Nurse Maureen</td>
                            <td>12:00am</td>
                            <td><button class="btn btn-outline-primary giveMedicationBtn"><i class="bi bi-clipboard-plus"></i></button></td>
                        </tr>
                        <tr>
                            <td>SH23/7865 Patrick Abiodun Aso</td>
                            <td>Mw Bed 1</td>
                            <td>Iv Flagyl</td>
                            <td>250mg 8hrly 2/7</td>
                            <td>Nurse Maureen</td>
                            <td>12:00am</td>
                            <td><button class="btn btn-outline-primary giveMedicationBtn"><i class="bi bi-clipboard-plus"></i></button></td>
                        </tr>
                        <tr>
                            <td>SH23/7865 Patrick Abiodun Aso</td>
                            <td>Mw Bed 1</td>
                            <td>Iv Flagyl</td>
                            <td>250mg 8hrly 2/7</td>
                            <td>Nurse Maureen</td>
                            <td>12:00am</td>
                            <td><button class="btn btn-outline-primary giveMedicationBtn"><i class="bi bi-clipboard-plus"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions1"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasWithBothOptionsLabel">List of Waiting Patients</h5>
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
            data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
            <i class="bi bi-list-check"></i>
            Medication/Treatment Table
        </button>
        <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas" id="waitingBtn"
        data-bs-target="#offcanvasWithBothOptions1" aria-controls="offcanvasWithBothOptions1">
        <i class="bi bi-list-check"></i>
        Waiting List
    </button>
    </div>

    <div>
        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-allPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-allPatients" 
                    type="button" role="tab" aria-controls="nav-allPatients" aria-selected="true">All Patients</button>

                <button class="nav-link" id="nav-ancPatients-tab" data-bs-toggle="tab" data-bs-target="#nav-ancPatients"
                    type="button" role="tab" aria-controls="nav-ancPatients" aria-selected="false">ANC Patients</button>

            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <!-- patients table -->
            <div class="tab-pane fade show active" id="nav-allPatients" role="tabpanel"
                aria-labelledby="nav-allPatients-tab" tabindex="0">
                <div class="py-4">
                    <table id="allPatientsTable" class="table table-hover align-middle table-sm">
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
                        <tbody>
                            {{-- <tr>
                                <td>20/09/23</td>
                                <td>SH23/7865 Patrick Abiodun Aso</td>
                                <td>QC30-Malingering</td>
                                <td>Dr Toby</td>
                                <td>Axe Mansard HMO</td>
                                <td>Out-patient</td>
                                <td><button class="btn btn-outline-primary detailsBtn">Details</button></td>
                            </tr>
                            <tr>
                                <td>21/05/22</td>
                                <td>SH21/4012 Josephine Ene Ode</td>
                                <td>QC30-Malingering</td>
                                <td>Dr Ralph</td>
                                <td>Self</td>
                                <td>In-patient</td>
                                <td><button class="btn btn-outline-primary detailsBtn">Details</button></td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- Anc table -->
            <div class="tab-pane fade" id="nav-ancPatients" role="tabpanel" aria-labelledby="nav-ancPatients-tab"
                tabindex="0">
                <div class="py-4 ">
                    <table id="ancPatientsTable" class="table table-hover align-middle table-sm">
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
        </div>
    </div>
</div>
@endsection