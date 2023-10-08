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
        // [
        //     'id' => 119,
        //     'date' => '08-Jul-2023',
        //     'client' => 'Helen',
        //     'bloodPressure' => '145/90mmgh',
        //     'temperature' => '36.5',
        //     'sugarLevel' => '4.5mmlo',
        //     'respiratoryRate' => '25',
        //     'pulseRate' => 82,
        //     'height' => '1.42m',
        //     'weight' => '88kg',
        //     'activeUser' => 'Admin',
        // ],
        // [
        //     'id' => 118,
        //     'date' => '08-Jul-2023',
        //     'client' => 'Tabitha',
        //     'bloodPressure' => '90/60mmgh',
        //     'temperature' => '39.0',
        //     'sugarLevel' => '7.4mmlo',
        //     'respiratoryRate' => '42',
        //     'pulseRate' => 67,
        //     'height' => '1.2m',
        //     'weight' => '56kg',
        //     'activeUser' => 'Admin',
        // ],
        // [
        //     'id' => 117,
        //     'date' => '08-Jul-2023',
        //     'client' => 'Adorable Event',
        //     'bloodPressure' => '160/110mmgh',
        //     'temperature' => '37.2',
        //     'sugarLevel' => '5.0mmlo',
        //     'respiratoryRate' => '32',
        //     'pulseRate' => 80,
        //     'height' => '1.45m',
        //     'weight' => '96kg',
        //     'activeUser' => 'Admin',
        // ],
        // [
        //     'id' => 116,
        //     'date' => '08-Jul-2023',
        //     'client' => 'Mrs Grace',
        //     'bloodPressure' => '110/70mmgh',
        //     'temperature' => '38.5',
        //     'sugarLevel' => '3.8mmlo',
        //     'respiratoryRate' => '30',
        //     'pulseRate' => 75,
        //     'height' => '1.32m',
        //     'weight' => '77kg',
        //     'activeUser' => 'Admin',
        // ],
        // [
        //     'id' => 115,
        //     'date' => '08-Jul-2023',
        //     'client' => 'CONESAM NG',
        //     'bloodPressure' => '120/90mmgh',
        //     'temperature' => '38.1',
        //     'sugarLevel' => '5.6mmlo',
        //     'respiratoryRate' => '27',
        //     'pulseRate' => 88,
        //     'height' => '1.5m',
        //     'weight' => '103',
        //     'activeUser' => 'Admin',
        // ],
        // [
        //     'id' => 114,
        //     'date' => '08-Jul-2023',
        //     'client' => 'ORENDA',
        //     'bloodPressure' => '140/100mmgh',
        //     'temperature' => '37.5',
        //     'sugarLevel' => '9.0mmlo',
        //     'respiratoryRate' => '29',
        //     'pulseRate' => 90,
        //     'height' => '1.3m',
        //     'weight' => '65kg',
        //     'activeUser' => 'Admin',
        // ],
        // [
        //     'id' => 113,
        //     'date' => '08-Jul-2023',
        //     'client' => 'Naomi',
        //     'bloodPressure' => '125/70mmgh',
        //     'temperature' => '39.2',
        //     'sugarLevel' => '8.4mmlo',
        //     'respiratoryRate' => '15Studio pictures',
        //     'pulseRate' => 74,
        //     'height' => '1.2m',
        //     'weight' => '70kg',
        //     'activeUser' => 'Admin',
        // ],
        // [
        //     'id' => 112,
        //     'date' => '07-Jul-2023',
        //     'client' => 'Casandra',
        //     'bloodPressure' => '130/90mmgh',
        //     'temperature' => '37.8',
        //     'sugarLevel' => '3.4mmlo',
        //     'respiratoryRate' => '32',
        //     'pulseRate' => 80,
        //     'height' => '1.35m',
        //     'weight' => '67kg',
        //     'activeUser' => 'Admin',
        // ],
        // [
        //     'id' => 111,
        //     'date' => '07-Jul-2023',
        //     'client' => 'Daniel',
        //     'bloodPressure' => '150/100mmgh',
        //     'temperature' => '36.9',
        //     'sugarLevel' => '9.6mmlo',
        //     'respiratoryRate' => '28',
        //     'pulseRate' => 100,
        //     'height' => '1.4m',
        //     'weight' => '89kg',
        //     'activeUser' => 'Admin',
        // ],
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
                            </div>
                        </div>
                        <div id="treatmentDiv">
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
