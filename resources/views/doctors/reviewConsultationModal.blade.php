<?php
$consultationDetails = [
    'data' => [
        [
            'id' => 120,
            'date' => '10-Jul-2023',
            'name' => 'Naomi',
            'bloodPressure' => '120/80mmgh',
            'temperature' => '37.6',
            'sugarLevel' => '5.0mmlo',
            'respiratoryRate' => '30',
            'pulseRate' => 95,
            'height' => '1.6m',
            'weight' => '102kg',
            'activeUser' => 'Admin',
        ],
        [
            'id' => 119,
            'date' => '08-Jul-2023',
            'client' => 'Helen',
            'bloodPressure' => '145/90mmgh',
            'temperature' => '36.5',
            'sugarLevel' => '4.5mmlo',
            'respiratoryRate' => '25',
            'pulseRate' => 82,
            'height' => '1.42m',
            'weight' => '88kg',
            'activeUser' => 'Admin',
        ],
        [
            'id' => 118,
            'date' => '08-Jul-2023',
            'client' => 'Tabitha',
            'bloodPressure' => '90/60mmgh',
            'temperature' => '39.0',
            'sugarLevel' => '7.4mmlo',
            'respiratoryRate' => '42',
            'pulseRate' => 67,
            'height' => '1.2m',
            'weight' => '56kg',
            'activeUser' => 'Admin',
        ],
        [
            'id' => 117,
            'date' => '08-Jul-2023',
            'client' => 'Adorable Event',
            'bloodPressure' => '160/110mmgh',
            'temperature' => '37.2',
            'sugarLevel' => '5.0mmlo',
            'respiratoryRate' => '32',
            'pulseRate' => 80,
            'height' => '1.45m',
            'weight' => '96kg',
            'activeUser' => 'Admin',
        ],
        [
            'id' => 116,
            'date' => '08-Jul-2023',
            'client' => 'Mrs Grace',
            'bloodPressure' => '110/70mmgh',
            'temperature' => '38.5',
            'sugarLevel' => '3.8mmlo',
            'respiratoryRate' => '30',
            'pulseRate' => 75,
            'height' => '1.32m',
            'weight' => '77kg',
            'activeUser' => 'Admin',
        ],
        [
            'id' => 115,
            'date' => '08-Jul-2023',
            'client' => 'CONESAM NG',
            'bloodPressure' => '120/90mmgh',
            'temperature' => '38.1',
            'sugarLevel' => '5.6mmlo',
            'respiratoryRate' => '27',
            'pulseRate' => 88,
            'height' => '1.5m',
            'weight' => '103',
            'activeUser' => 'Admin',
        ],
        [
            'id' => 114,
            'date' => '08-Jul-2023',
            'client' => 'ORENDA',
            'bloodPressure' => '140/100mmgh',
            'temperature' => '37.5',
            'sugarLevel' => '9.0mmlo',
            'respiratoryRate' => '29',
            'pulseRate' => 90,
            'height' => '1.3m',
            'weight' => '65kg',
            'activeUser' => 'Admin',
        ],
        [
            'id' => 113,
            'date' => '08-Jul-2023',
            'client' => 'Naomi',
            'bloodPressure' => '125/70mmgh',
            'temperature' => '39.2',
            'sugarLevel' => '8.4mmlo',
            'respiratoryRate' => '15Studio pictures',
            'pulseRate' => 74,
            'height' => '1.2m',
            'weight' => '70kg',
            'activeUser' => 'Admin',
        ],
        [
            'id' => 112,
            'date' => '07-Jul-2023',
            'client' => 'Casandra',
            'bloodPressure' => '130/90mmgh',
            'temperature' => '37.8',
            'sugarLevel' => '3.4mmlo',
            'respiratoryRate' => '32',
            'pulseRate' => 80,
            'height' => '1.35m',
            'weight' => '67kg',
            'activeUser' => 'Admin',
        ],
        [
            'id' => 111,
            'date' => '07-Jul-2023',
            'client' => 'Daniel',
            'bloodPressure' => '150/100mmgh',
            'temperature' => '36.9',
            'sugarLevel' => '9.6mmlo',
            'respiratoryRate' => '28',
            'pulseRate' => 100,
            'height' => '1.4m',
            'weight' => '89kg',
            'activeUser' => 'Admin',
        ],
    ],
];
?>


<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="mb-2 form-control">
                            @include('patients.partials.patientBio')
                        </div>
                        <div class="mb-2 form-control">
                            <X-form-span class="fw-semibold">Previously Known Clinical Info</X-form-span>
                            <div class="row knownClinicalInfoDiv">
                                @include('patients.partials.known-clinical-info', ['disabled' => true])
                                <div class="d-flex justify-content-center">
                                    <button type="button" id=""
                                        class="btn bg-primary text-white reviewKnownClinicalInfoBtn">
                                        <i class="bi bi-arrow-up-circle"></i>
                                        Update
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="consultationReviewDiv">
                        </div>
                        <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center" id="collapseReview" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="true" aria-controls="collapseExample">
                            <span class="mx-2">Review Patient</span>
                            <i class="bi bi-chevron-double-down text-primary"> </i>
                        </div>
                        <div class="collapse mb-2 reviewDiv" id="collapseExample" style="">
                            <div class="card card-body">
                                <div id="reviewConsultationDiv">
                                    <div class="mb-2 form-control">
                                        <x-form-label>Review Patient</x-form-label>
                                        <div class="row">
                                            <div class="row addReviewVitalsignsDiv d-none">
                                                <x-form-span class="fw-semibold">Vital Signs</x-form-span>
                                                @include('vitalsigns.vitalsigns', ['disabled' => true])
                                            </div>
                                            <div class="d-flex justify-content-center">
                                                <button type="button" id="addReviewVitalsignsBtn"
                                                    class="btn bg-primary text-white">
                                                    <i class="bi bi-bag-plus"></i>
                                                    vital signs
                                                </button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <x-form-span class="fw-semibold">Consultation Review</x-form-span>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="complainLabel">Complain</x-input-span>
                                                <x-form-textarea name="complain" id="complain" cols="10"
                                                    rows="3"></x-form-textarea>
                                            </x-form-div>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span class="" id="notesLabel"> Notes </x-input-span>
                                                <x-form-textarea class="" name="notes" id="notes" cols="10"
                                                    rows="3"></x-form-textarea>
                                            </x-form-div>
                                            <x-form-div class="col-xl-12">
                                                <x-input-span class="" id="examinationFindingsLabel"> Examination <br>
                                                    Findings </x-input-span>
                                                <x-form-textarea name="examinationFindings" id="examinationFindings"
                                                    cols="10" rows="3"></x-form-textarea>
                                            </x-form-div>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span class="" id="assessmentLabel"> Assessment
                                                </x-input-span>
                                                <x-form-textarea class="assessment" name="assessment" cols="10"
                                                    rows="3"></x-form-textarea>
                                            </x-form-div>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span class="" id="planLabel">Plan</x-input-span>
                                                <x-form-textarea class="" name="plan" id="plan" cols="10"
                                                    rows="3"></x-form-textarea>
                                            </x-form-div>
                                        </div>
                                        <div class="row my-2">
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="admitLabel">Admit?</x-input-span>
                                                <select class="form-select form-select-md" name="admit">
                                                    <option value="">Select</option>
                                                    <option value="Outpatient">No</option>
                                                    <option value="Inpatient">Yes</option>
                                                </select>
                                            </x-form-div>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="wardLabel">Ward</x-input-span>
                                                <select class="form-select form-select-md" name="ward">
                                                    <option value="">Select Ward</option>
                                                    <option value="Private Ward">Private Ward</option>
                                                    <option value="General Ward">General Ward</option>
                                                </select>
                                            </x-form-div>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" id="saveReviewConsultationBtn"
                                                class="btn bg-primary text-white">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Save
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="reviewInvestigationAndManagmentDiv d-none">
                                    <div class="mb-2 form-control">
                                        <x-form-span>Investigation & Management</x-form-span>
                                        <div class="row">
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="itemsLabel">Item</x-input-span>
                                                <x-form-input type="search" name="item" id="item" placeholder="search" list="itemsList"/>
                                                <datalist name="item" type="text" class="decoration-none" id="itemsList"></datalist>
                                            </x-form-div>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="prescriptionLabel">Prescription</x-input-span>
                                                <x-form-input type="text" name="prescription" id="prescription"
                                                    placeholder="eg: 5mg BD x5" />
                                            </x-form-div>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="quantityLabel">Quantity</x-input-span>
                                                <x-form-input type="number" name="quantity" id="quantity"
                                                    placeholder="" />
                                            </x-form-div>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" id="addInvestigationAndManagmnentBtn"
                                                class="btn btn-primary">
                                                add
                                                <i class="bi bi-prescription"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-2 form-control">
                                        <table id="prescriptionTable"
                                            class="table table-hover align-middle table-sm bg-primary">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Billed at</th>
                                                    <th>Item</th>
                                                    <th>Prescription</th>
                                                    <th>Qty</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>12/09/2023 11:02pm</td>
                                                    <td>N/S 500mls</td>
                                                    <td>500mls 12hrly x2</td>
                                                    <td></td>
                                                    <td><button class="btn btn-outline-primary deleteBtn"><i
                                                                class="bi bi-trash"></i></button></td>
                                                </tr>
                                                <tr>
                                                    <td>2</td>
                                                    <td>12/09/2023 11:15pm</td>
                                                    <td>5% mls Syringe</td>
                                                    <td></td>
                                                    <td>4</td>
                                                    <td><button class="btn btn-outline-primary deleteBtn"><i
                                                                class="bi bi-trash"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-center mb-1 text-outline-primary input-group-text text-center"  id="collapseReview" data-bs-toggle="collapse" href="#collapseExample" role="button" aria-expanded="true" aria-controls="collapseExample">
                                    <span class="mx-2">Close Review</span>
                                    <i class="bi bi-chevron-double-up text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    {{-- <button type="button" id="saveBtn" class="btn bg-primary text-white">
                        <i class="bi bi-check-circle me-1"></i>
                        Save
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
</div>
