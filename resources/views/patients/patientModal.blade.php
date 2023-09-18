<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-form-label>Patient's Overall Information</x-form-label>
                    <div class="row">
                        <x-form-div class="">
                            <x-input-span id="cardTypeLabel">Card Type<x-required-span /></x-input-span>
                            <select class="form-select form-select-md cardType" name="cardType"
                                aria-label="card-type" name="cardType">
                                <option value="">Select card Type</option>
                                <option {{ $isUpdate ? 'hidden' : '' }} value="Regular.New">Regular New</option>
                                <option {{ $isUpdate ? 'hidden' : '' }} value="Regular.Old">Regular Old</option>
                                <option {{ !$isUpdate ? 'hidden' : '' }} value="Regular">Regualar</option>
                                <option class="ancOption" value="ANC">ANC</option>
                            </select>
                        </x-form-div>
                        <x-input-error :messages="$errors->get('cardType')" class="mt-1" />
                    </div>
                    <div class="{{ !$isUpdate ? 'd-none' : '' }} allPatientInputsDiv">
                        <div class="mb-2">
                            <x-form-span>Hospital Links</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="sponsorCategoryLabel">Sponsor Category<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md sponsorCategory"
                                       name="sponsorCategory">
                                        <option value="">Select Category</option>
                                        <option value="Self">Self</option>
                                        <option class="familyOption" value="Family">Family</option>
                                        <option value="NHIS">NHIS</option>
                                        <option value="HMO">HMO</option>
                                        <option value="Organization">Organization</option>
                                    </select>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('sponsorCategory')" class="mt-1" />
                                <x-form-div class="">
                                    <x-input-span id="cardNumberLabel">Card Number<x-required-span /></x-input-span>
                                    <x-form-input type="text" class="{{ $isUpdate ? 'd-none' : '' }} newCardNumber" data-maska="SH{{ date('y') }}/####A"
                                         aria-label="cardNumber" aria-describedby="basic-addon1" :isUpdate="$isUpdate" />
                                    <x-form-input type="text" class="{{ $isUpdate ? 'd-none' : '' }} oldCardNumber" data-maska="SH##/####A"
                                        aria-label="cardNumber" aria-describedby="basic-addon1" :isUpdate="$isUpdate" />
                                    <x-form-input type="text" class="{{ $isUpdate ? 'd-none' : '' }} ancCardNumber" data-maska="ANC{{ date('y') }}/####" 
                                        aria-label="cardNumber" aria-describedby="basic-addon1" :isUpdate="$isUpdate"  />
                                    <input type="text" aria-label="cardNumber" aria-describedby="basic-addon1" class="form-control" {!! $isUpdate ? 'disabled name="cardNumber" value="SH23/0024"'  : 'hidden' !!}>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('cardNumber')" class="mt-1" />

                                <x-form-div class="registrationBillDiv d-none">
                                    <x-input-span id="registrationBillLabel">Registration Bill<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md selfRegistrationBill"
                                        aria-label="registrationBill">
                                        <option value="2000">2000</option>
                                    </select>
                                    <select class="form-select form-select-md familyRegistrationBill d-none"
                                        aria-label="registrationBill">
                                        <option value="">Select Option</option>
                                        <option value="1500">1500 - Upgrade</option>
                                        <option class="familyRegistrationBillOption" value="3500">3500 - New</option>
                                        <option value="Paid">Paid</option>
                                    </select>
                                    <select class="form-select form-select-md ancRegistrationBill d-none"
                                        aria-label="registrationBill">
                                        <option value="1000">1000</option>
                                    </select>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('registrationBill')" class="mt-1" />
                                <x-form-div class="sponsorNameDiv">
                                    <x-input-span id="sponsorNameLabel">Sponsor Name<x-required-span /></x-input-span>
                                    <x-form-input type="search" class="sponsorName"
                                        placeholder="Search..." list="sponsorList" />
                                    <datalist name="sponsor" type="text" class="decoration-none bg-white"
                                        id="sponsorList">
                                        <option id="clientOption" value="Police NHIS" data-id="13"
                                            name="Police NHIS">
                                        </option>
                                    </datalist>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('sponsorName')" class="mt-1" />
                                <x-form-div class="staffIdDiv">
                                    <x-input-span>Staff ID/No.</x-input-span>
                                    <x-form-input name="staffId" class="staffId" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('staffId')" class="mt-1" />
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-form-span>Bio</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="firstNameLabel">First Name<x-required-span /></x-input-span>
                                    <x-form-input name="firstName" id="firstName" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('firstName')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span id="middleNameLabel">Middle Name</x-input-span>
                                    <x-form-input name="middleName" id="middleName" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('middleName')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span id="surnameLabel">Surname<x-required-span /></x-input-span>
                                    <x-form-input name="surname" id="surname" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('surname')" class="mt-1" />
                            </div>
                            <!-- second row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="dateOfBirthLabel">Date of Birth<x-required-span /></x-input-span>
                                    <x-form-input type="date" name="dateOfBirth" id="dateOfBirth" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('dateOfBirth')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span id="dateOfBirthLabel">Sex<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md sex" aria-label="sex" name="sex">
                                        <option value="">Select</option>
                                        <option value="Female">Female</option>
                                        <option value="Male">Male</option>
                                    </select>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('sex')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span id="maritalStatusLabel">Marital Status<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md" aria-label="marital-status"
                                        name="maritalStatus">
                                        <option value="">Select</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widow">Widow</option>
                                        <option value="Widower">Widower</option>
                                        <option value="Divorced">Divorced</option>
                                    </select>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('maritalStatus')" class="mt-1" />
                            </div>
                            <!-- Third row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span>Phone Number<x-required-span /></x-input-span>
                                    <x-form-input type="tel" name="phoneNumber" id="phoneNumber" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('phoneNumber')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span>Address<x-required-span /></x-input-span>
                                    <x-form-input name="address" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('address')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span>State Residence<x-required-span /></x-input-span>
                                    <x-select-states name="stateResidence" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('stateResidence')" class="mt-1" />
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-form-span>Related Info</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span>Email<x-required-span /></x-input-span>
                                    <x-form-input type="email" name="email" id="email" placeholder="akpan12@gmail.com" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >Nationality<x-required-span /></x-input-span>
                                    <x-form-input name="nationality" placeholder="Nigerian" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('nationality')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >State Origin<x-required-span /></x-input-span>
                                    <x-select-states name="stateOrigin"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('stateOrigin')" class="mt-1" />
                            </div>
                            <!-- second row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span >Occupation<x-required-span /></x-input-span>
                                    <x-form-input name="occupation" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('occupation')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >Religion</x-input-span>
                                    <x-form-input name="religion" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('religion')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >Ethnic Group</x-input-span>
                                    <x-form-input name="ethnicGroup" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('ethnicGroup')" class="mt-1" />
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-form-span>Next of Kin Information</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span >Full Name</x-input-span>
                                    <x-form-input name="nokName" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('nokName')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >Phone Number</x-input-span>
                                    <x-form-input type="tel" name="nokPhoneNo"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('nokPhoneNo')" class="mt-1" />
                                <x-form-div>
                                    <x-input-span >Relationship</x-input-span>
                                    <x-select-nok name="relationship"></x-select-nok>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('relationship')" class="mt-1" />
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-semibold">Previously Known Clinical Info</span>
                            <!-- first row -->
                            <div class="row">
                                @include("patients.partials.known-clinical-info", ["readonly" => false  ])
                            </div>
                            <!-- second row -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="button" id="{{ $isUpdate ? 'saveBtn' : 'registerBtn' }}" class="btn bg-primary text-white">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $isUpdate ? 'Update' : 'Register' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
