@extends('layout')

@section('content')
@vite(['resources/js/hmo.js'])

@include('hmo.verifyModal', ['title' => 'Verify Patient', 'isUpdate' => false, 'id' => 'verifyModal'])
@include('hmo.treatmentDetailsModal', ['title' => 'Treatment Details', 'isUpdate' => false, 'id' => 'treatmentDetailsModal'])
@include('hmo.approvalModal', ['title' => 'Approve Medication/Treatment', 'isUpdate' => false, 'id' => 'approvalModal'])

    <div class="container p-1 mt-5 bg-white">
        <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasWithBothOptionsLabel">List of Waiting Patients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="py-4 ">
                    <table id="waitingListTable" class="table table-hover align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Sponsor</th>
                                <th>Status</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SH21/4012 Joesphine Ene Odeh</td>
                                <td>Avon HMO</td>
                                <td><span class="badge rounded-pill text-bg-secondary p-2">Waiting</span></td>
                                <td><i class="btn btn-outline-none text-primary bi bi-x-circle"></i></td>
                            </tr>
                            <tr>
                                <td>SH23/7865 Patrick Abiodun Aso</td>
                                <td>Axe Mansard HMO</td>
                                <td><span class="badge rounded-pill text-bg-light p-2">Dr Toby</span></td>
                                <td>
                                    {{-- <i class="btn btn-outline-none text-primary bi bi-x-circle"></i> --}}
                                </td>
                            </tr>
                            <tr>
                                <td>SH21/4012 John Okoro</td>
                                <td>Avon HMO</td>
                                <td><span class="badge rounded-pill text-bg-light p-2">Dr Bisoye</span></td>
                                <td>
                                    {{-- <i class="btn btn-outline-none text-primary bi bi-x-circle"></i> --}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="offcanvas offcanvas-top" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions2"
            aria-labelledby="offcanvasWithBothOptions2Label">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasWithBothOptions2Label2">Medication/Treatment List for Approval</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas2-body">
                <div class="p-2 ">
                    <table id="waitingListTable" class="table table-hover table-striped align-middle table-sm">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Sponsor</th>
                                <th>Diagnosis</th>
                                <th>Med/Treatment</th>
                                <th>Prescription</th>
                                <th>Qty</th>
                                <th>Bill</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SH21/4012 Joesphine Ene Odeh</td>
                                <td>Avon HMO</td>
                                <td>QR14-Gastrosnteritis</td>
                                <td>N/S 500mls</td>
                                <td>500mls 12hrly 3/7</td>
                                <td>6 Infusion(s)</td>
                                <td>6600</td>
                                <td><i class="btn btn-outline-none text-primary bi bi-check-circle" id="approvalBtn"></i></td>
                            </tr>
                            <tr>
                                <td>SH21/4012 Obaje Dickson</td>
                                <td>Liberty Blue HMO</td>
                                <td>PTR4C-Malaria unspecified</td>
                                <td>ACT (Armatem Soft Gel)</td>
                                <td>480/80mg bd 3/7</td>
                                <td>1 pack(s)</td>
                                <td>1500</td>
                                <td><i class="btn btn-outline-none text-primary bi bi-check-circle"></i></td>
                            </tr>
                            <tr>
                                <td>SH21/4012 Michael Okonkwo</td>
                                <td>Health Partners HMO</td>
                                <td>QR14-Sepsis Unspecified</td>
                                <td>Inj Ceftiraxone (unbranded)</td>
                                <td>1g 12hrly 2/7</td>
                                <td>4 Vials(s)</td>
                                <td>4800</td>
                                <td><i class="btn btn-outline-none text-primary bi bi-check-circle"></i></td>
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
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions2" aria-controls="offcanvasWithBothOptions2">
                <i class="bi bi-list-check"></i>
                Medication/Treatment Approval List
            </button>
        </div>
        {{-- <div class="text-start mb-4">
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas2"
                data-bs-target="#offcanvasWithBothOptions2" aria-controls="offcanvasWithBothOptions2">
                <i class="bi bi-list-check"></i>
                Medication/Treatment Approval List
            </button>
        </div> --}}

        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-verifyPatients-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-verifyPatients" type="button" role="tab" aria-controls="nav-verifyPatients"
                        aria-selected="true">Verification</button>

                    <button class="nav-link" id="nav-treatments-tab" data-bs-toggle="tab" data-bs-target="#nav-treatments"
                        type="button" role="tab" aria-controls="nav-treatments" aria-selected="false">Treatments</button>

                    <button class="nav-link" id="nav-bills-tab" data-bs-toggle="tab" data-bs-target="#nav-bills"
                        type="button" role="tab" aria-controls="nav-bills" aria-selected="false">Bills</button>

                    <button class="nav-link" id="nav-reporst-tab" data-bs-toggle="tab" data-bs-target="#nav-reports"
                        type="button" role="tab" aria-controls="nav-reports" aria-selected="false">Reports</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-verifyPatients" role="tabpanel"
                    aria-labelledby="nav-verifyPatients-tab" tabindex="0">
                    <div class="py-4">
                        <table id="yourPatientsTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>20/09/23</td>
                                    <td>SH23/7865 Patrick Abiodun Aso</td>
                                    <td>Axe Mansard HMO</td>
                                    <td><button class="btn btn-outline-primary" id="verifyBtn">Verify</button></td>
                                </tr>
                                <tr>
                                    <td>21/05/22</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>Hygia HMO</td>
                                    <td><button class="btn btn-outline-primary">Verify</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- treatments table -->
                <div class="tab-pane fade" id="nav-treatments" role="tabpanel" aria-labelledby="nav-treatments-tab"
                    tabindex="0">
                    <div class="py-4">
                        <table id="treatmentsTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09/10/2023</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>Axe Mansard</td>
                                    <td>Dr Toby</td>
                                    <td>F12Z-Acute Spundolosis</td>
                                    <td>Out-Patient</td>
                                    <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Details</button>
                                        {{-- <button class="btn btn-outline-primary" id="reviewConsultationBtn">Approve</button> --}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- bills table -->
                <div class="tab-pane fade" id="nav-bills" role="tabpanel" aria-labelledby="nav-bills-tab"
                    tabindex="0">
                    <div class="py-4 ">
                        <table id="sponsorsTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Bill Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09/10/2023</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>Axe Mansard</td>
                                    <td>Dr Toby</td>
                                    <td>F12Z-Acute Spundolosis</td>
                                    <td>Out-Patient</td>
                                    <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Sent</button>
                                        {{-- <button class="btn btn-outline-primary" id="reviewConsultationBtn">Approve</button> --}}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- reports table -->
                <div class="tab-pane fade" id="nav-reports" role="tabpanel" aria-labelledby="nav-reports-tab"
                    tabindex="0">
                    <div class="py-4 justify-content-center">
                        <table id="reportsTable" class="table table-hover align-center table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Verified</th>
                                    <th>Treatment</th>
                                    <th>Bill-Sent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09/10/2023</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>Axe Mansard</td>
                                    <td>Dr Toby</td>
                                    <td>F12Z-Acute Spundolosis</td>
                                    <td>Out-Patient</td>
                                    <td class="fst-italic">Pending</td>
                                    <td class="fst-italic">No Code</td>
                                    <td class="fst-italic">Not Sent</td>
                                    {{-- <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Sent</button>
                                    </td> --}}
                                </tr>
                                <tr>
                                    <td>08/10/2023</td>
                                    <td>SH21/1403 Shine Ewara</td>
                                    <td>Health Partners</td>
                                    <td>Dr Tony</td>
                                    <td>F12Z-Severe Malaria</td>
                                    <td>In-Patient</td>
                                    <td class="fst-italic">Verified</td>
                                    <td class="fst-italic">HP-45srt6if1</td>
                                    <td class="fst-italic">Sent</td>
                                    {{-- <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Sent</button>
                                    </td> --}}
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
