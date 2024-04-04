@vite(['resources/js/modals/patientModal.js'])
<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-form-label>Patient's Overall Information</x-form-label>
                    <div class="row">
                        <x-form-div class="">
                            <x-input-span id="patientTypeLabel">Patient Type<x-required-span /></x-input-span>
                            <select class="form-select form-select-md patientType" aria-label="patientType" name="patientType">
                                <option value="">Select card Type</option>
                                <option {{ $isUpdate ? 'hidden' : '' }} value="Regular.New">Regular New</option>
                                <option {{ $isUpdate ? 'hidden' : '' }} value="Regular.Old">Regular Old</option>
                                <option {{ !$isUpdate ? 'hidden' : '' }} value="Regular">Regular</option>
                                <option class="ancOption" value="ANC">ANC</option>
                            </select>
                        </x-form-div>

                    </div>
                    <div class="{{ !$isUpdate ? 'd-none' : '' }} allPatientInputsDiv form-control">
                        <div class="mb-2">
                            <x-form-span>Hospital Links</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="sponsorCategoryLabel">Sponsor Category<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md" name="sponsorCategory" id="{{ $isUpdate ? 'updateSponsorCategory' : 'newSponsorCategory' }}">
                                        <option value="">Select Category</option>p
                                        @foreach ($categories as $category )
                                        <option @if (Str::lower($category->name) === "family") {!! 'class="familyOption"' !!} @endif value="{{ $category->id}}" name="{{ $category->name }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span id="cardNumberLabel">Card Number<x-required-span /></x-input-span>
                                    <x-form-input type="text" class="{{ $isUpdate ? 'd-none' : '' }} newCardNumber" data-maska="SH{{ date('y') }}/####A"
                                         aria-label="cardNumber" aria-describedby="basic-addon1" :isUpdate="$isUpdate"/>
                                    <x-form-input type="text" class="{{ $isUpdate ? 'd-none' : '' }} oldCardNumber" data-maska="SH##/####A"
                                        aria-label="cardNumber" aria-describedby="basic-addon1" :isUpdate="$isUpdate"/>
                                    <x-form-input type="text" class="{{ $isUpdate ? 'd-none' : '' }} ancCardNumber" data-maska="ANC##/####" 
                                        aria-label="cardNumber" aria-describedby="basic-addon1" :isUpdate="$isUpdate" />
                                    <input type="text" aria-label="cardNumber" aria-describedby="basic-addon1" class="form-control" {!! $isUpdate ? 'name="cardNumber" disabled'  : 'hidden' !!}>
                                </x-form-div>

                                <x-form-div class="registrationBillDiv d-none">
                                    <x-input-span id="registrationBillLabel">Registration Bill<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md selfRegistrationBill" aria-label="registrationBill">
                                        <option value="2000">2000</option>
                                    </select>
                                    <select class="form-select form-select-md ancRegistrationBill d-none" aria-label="registrationBill">
                                        <option value="1000">1000</option>
                                    </select>
                                </x-form-div>

                                <x-form-div class="sponsorNameDiv">
                                    <x-input-span id="sponsorNameLabel">Sponsor<x-required-span /></x-input-span>
                                    <x-form-input type="search" name="sponsor" class="sponsorName categorySponsor" id="{{ $isUpdate ? 'updatePatientSponsor' : 'newPatientSponsor' }}" placeholder="Search..." list="{{ $isUpdate ? 'updateSponsorList' : 'newSponsorList' }}"/>
                                    <datalist name="sponsor" type="text" class="decoration-none bg-white sponsorList" id="{{ $isUpdate ? 'updateSponsorList' : 'newSponsorList' }}"></datalist>
                                </x-form-div>

                                <x-form-div class="staffIdDiv">
                                    <x-input-span>Staff ID/No.</x-input-span>
                                    <x-form-input name="staffId" class="staffId" value=""/>
                                </x-form-div>
                                    
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
                                    <x-form-input type="date" name="dateOfBirth" id="dateOfBirth"/>
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
                                    <x-input-span>Phone Number<x-required-span /></x-input-span>
                                    <x-form-input type="number" name="phone" id="phone"/>
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
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="button" id="{{ $isUpdate ? 'savePatientBtn' : 'registerPatientBtn' }}" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $isUpdate ? 'Update' : 'Register' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
