<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="p-md-4 border rounded-3 " id="submitDiv">
                        <div>
                        
                            <x-form-label class="">Patient's Overall Information</x-form-label>
                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span id="patientTypeLabel">Patient Type<x-required-span /></x-input-span>
                                    <x-form-input class=" patientType" aria-label="patientType" name="patientType" value="{{ $preForm->patient_type }}" disabled/>
                                </x-form-div>
                            </div>
                            <div class="my-2">
                                <x-form-span>Hospital Links</x-form-span>
                                <!-- first row -->
                                <div class="row">
                                    <x-form-div>
                                        <x-input-span id="sponsorCategoryLabel">Sponsor Category<x-required-span /></x-input-span>
                                        <x-form-input class="form-select form-select-md" name="sponsorCategory" id="newSponsorCategory" value="{{ $preForm->sponsor_category }}" disabled />
                                    </x-form-div>

                                    <x-form-div class="">
                                        <x-input-span id="cardNumberLabel">Card Number<x-required-span /></x-input-span>
                                        <x-form-input type="text" aria-label="cardNumber" aria-describedby="basic-addon1" class="form-control" name="cardNumber" value="{{ $preForm->card_no }}" disabled />
                                    </x-form-div>

                                    <x-form-div class="sponsorNameDiv">
                                        <x-input-span id="sponsorNameLabel">Sponsor<x-required-span /></x-input-span>
                                        <x-form-input type="search" name="sponsor" class="sponsorName" id="newPatientSponsor" value="{{ $preForm->sponsor_id }}" disabled/>
                                    </x-form-div>

                                    <x-form-div class="staffIdDiv">
                                        <x-input-span>Staff ID/No.</x-input-span>
                                        <x-form-input name="staffId" class="staffId" value=""/>
                                    </x-form-div>
                                    {{-- <x-form-div class="">
                                        <x-input-span>Flag Patient</x-input-span>
                                        <select class="form-select form-select-md" name="flagPatient">
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>   
                                        </select>
                                    </x-form-div>
                                    <x-form-div class="">
                                        <x-input-span>Flag Reason</x-input-span>
                                        <x-form-input name="flagReason" class="FlagReason" value=""/>
                                    </x-form-div> --}}
                                </div>
                            </div>
                            <div class="mb-2">
                                <x-form-span>Bio</x-form-span>
                                <!-- first row -->
                                <div class="row">
                                    <x-form-div>
                                        <x-input-span id="firstNameLabel">First Name<x-required-span /></x-input-span>
                                        <x-form-input name="firstName" id="firstName"/>
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span id="middleNameLabel">Middle Name</x-input-span>
                                        <x-form-input name="middleName" id="middleName"/>
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span id="lastnameLabel">Last name<x-required-span /></x-input-span>
                                        <x-form-input name="lastName" id="lastname"/>
                                    </x-form-div>
                                </div>
                                <!-- second row -->
                                <div class="row">
                                    <x-form-div>
                                        <x-input-span id="dateOfBirthLabel">Date of Birth<x-required-span /></x-input-span>
                                        <x-form-input type="datetime-local" name="dateOfBirth" id="dateOfBirth"/>
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span id="dateOfBirthLabel">Sex<x-required-span /></x-input-span>
                                        <select class="form-select form-select-md sex" aria-label="sex" name="sex" id="sex">
                                            <option value="">Select</option>
                                            <option value="Female">Female</option>
                                            <option value="Male">Male</option>
                                        </select>
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span id="maritalStatusLabel">Marital Status<x-required-span /></x-input-span>
                                        <select class="form-select form-select-md" aria-label="marital-status"
                                            name="maritalStatus" id="maritalStatus">
                                            <option value="">Select</option>
                                            <option value="Single">Single</option>
                                            <option value="Married">Married</option>
                                            <option value="Widow">Widow</option>
                                            <option value="Widower">Widower</option>
                                            <option value="Divorced">Divorced</option>
                                        </select>
                                    </x-form-div>
                                </div>
                                <!-- Third row -->
                                <div class="row">
                                    <x-form-div>
                                        <x-input-span>Phone<x-required-span /></x-input-span>
                                        <x-form-input type="number" name="phone" id="phone" value="{{ $preForm->phone }}" style="width:7rem;"/>
                                        <x-input-span id="smsLabel">SMS<x-required-span /></x-input-span>
                                        <select class="form-select form-select-md" aria-label="sms"
                                            name="sms" id="sms">
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                    </x-form-div>
                                    
                                    <x-form-div>
                                        <x-input-span>Address<x-required-span /></x-input-span>
                                        <x-form-input name="address" id="address"/>
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span>State Residence<x-required-span /></x-input-span>
                                        <x-select-states name="stateResidence" id="stateResidence"/>
                                    </x-form-div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <x-form-span>Related Info</x-form-span>
                                <!-- first row -->
                                <div class="row">
                                    <x-form-div>
                                        <x-input-span>Email</x-input-span>
                                        <x-form-input type="email" name="email" id="email" placeholder="akpan12@gmail.com" />
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span >Nationality</x-input-span>
                                        <x-form-input name="nationality" id="nationality" placeholder="eg. Nigerian" />
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span >State Origin</x-input-span>
                                        <x-select-states name="stateOrigin" id="stateOfOrigin"/>
                                    </x-form-div>
                                </div>
                                <!-- second row -->
                                <div class="row">
                                    <x-form-div>
                                        <x-input-span >Occupation</x-input-span>
                                        <x-form-input name="occupation" id="occupation"/>
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span >Religion</x-input-span>
                                        <x-form-input name="religion" id="religion"/>
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span >Ethnic Group</x-input-span>
                                        <x-form-input name="ethnicGroup" id="ethnicGroup"/>
                                    </x-form-div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <x-form-span>Next of Kin Information</x-form-span>
                                <!-- first row -->
                                <div class="row">
                                    <x-form-div>
                                        <x-input-span >Full Name<x-required-span /></x-input-span>
                                        <x-form-input name="nextOfKin" id="nextOfKin"/>
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span >Phone Number<x-required-span /></x-input-span>
                                        <x-form-input type="number" name="nextOfKinPhone" id="nextOfKinPhone"/>
                                    </x-form-div>

                                    <x-form-div>
                                        <x-input-span >Relationship<x-required-span /></x-input-span>
                                        <x-select-nok name="nextOfKinRship" id="nextOfKinRship"></x-select-nok>
                                    </x-form-div>
                                </div>
                            </div>
                            <div class="mb-2">
                                <span class="fw-semibold">Previously Known Clinical Info</span>
                                <!-- first row -->
                                <div class="row">
                                    @include("patients.partials.known-clinical-info", ["disabled" => false, "readonly" => false])
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" id="submitPatientBtn" class="btn btn-primary" data-id="{{ $preForm->id }}">
                        <i class="bi bi-check-circle me-1"></i>
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>