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
                            <select class="form-select form-select-md patientType" aria-label="patientType" :value="old('sponsorCategory')"  name="patientType">
                                <option value="">Select card Type</option>
                                <option {{ $isUpdate ? 'hidden' : '' }} value="Regular.New">Regular New</option>
                                <option {{ $isUpdate ? 'hidden' : '' }} value="Regular.Old">Regular Old</option>
                                <option {{ !$isUpdate ? 'hidden' : '' }} value="Regular">Regualar</option>
                                <option class="ancOption" value="ANC">ANC</option>
                            </select>
                        </x-form-div>
                        <x-input-error :messages="$errors->get('patientType')" class="mt-1" />
                    </div>
                    <div class="{{ !$isUpdate ? 'd-none' : '' }} allPatientInputsDiv form-control">
                        <div class="mb-2">
                            <x-form-span>Hospital Links</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="sponsorCategoryLabel">Sponsor Category<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md sponsorCategory" :value="old('sponsorCategory')" name="sponsorCategory">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $category )
                                        <option @if (Str::lower($category->name) === "family") {!! 'class="familyOption"' !!} @endif value="{{ $category->id}}" name="{{ $category->name }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('sponsorCategory')" class="mt-1" />

                                <x-form-div class="">
                                    <x-input-span id="cardNumberLabel">Card Number<x-required-span /></x-input-span>
                                    <x-form-input type="text" class="{{ $isUpdate ? 'd-none' : '' }} newCardNumber" data-maska="SH{{ date('y') }}/####A"
                                         aria-label="cardNumber" aria-describedby="basic-addon1" :isUpdate="$isUpdate" :value="old('cardNumber')"/>
                                    <x-form-input type="text" class="{{ $isUpdate ? 'd-none' : '' }} oldCardNumber" data-maska="SH##/####A"
                                        aria-label="cardNumber" aria-describedby="basic-addon1" :isUpdate="$isUpdate" :value="old('cardNumber')"/>
                                    <x-form-input type="text" class="{{ $isUpdate ? 'd-none' : '' }} ancCardNumber" data-maska="ANC{{ date('y') }}/####" 
                                        aria-label="cardNumber" aria-describedby="basic-addon1" :isUpdate="$isUpdate"  :value="old('cardNumber')"/>
                                    <input type="text" aria-label="cardNumber" aria-describedby="basic-addon1" class="form-control" {!! $isUpdate ? 'disabled name="cardNumber" value="SH23/0024"'  : 'hidden' !!}>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('cardNumber')" class="mt-1" />

                                <x-form-div class="registrationBillDiv d-none">
                                    <x-input-span id="registrationBillLabel">Registration Bill<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md selfRegistrationBill"
                                        aria-label="registrationBill" :value="old('registrationBill')">
                                        <option value="2000">2000</option>
                                    </select>
                                    <select class="form-select form-select-md ancRegistrationBill d-none"
                                        aria-label="registrationBill" :value="old('registerationBill')">
                                        <option value="1000">1000</option>
                                    </select>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('registrationBill')" class="mt-1" />

                                <x-form-div class="sponsorNameDiv">
                                    <x-input-span id="sponsorNameLabel">Sponsor<x-required-span /></x-input-span>
                                    <x-form-input type="search" name="sponsor" class="sponsorName" id="sponsor" placeholder="Search..." list="sponsorList" :value="old('sponsor')"/>
                                    <datalist name="sponsor" type="text" class="decoration-none bg-white" id="sponsorList">
                                        {{-- <option id="sponsorOption" value="Police NHIS" data-id="13" name="Police NHIS"></option> --}}
                                    </datalist>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('sponsorName')" class="mt-1" />

                                <x-form-div class="staffIdDiv">
                                    <x-input-span>Staff ID/No.</x-input-span>
                                    <x-form-input name="staffId" class="staffId" :value="old('staffId')"/>
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
                                    <x-form-input name="firstName" id="firstName" :value="old('firstName')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('firstName')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span id="middleNameLabel">Middle Name</x-input-span>
                                    <x-form-input name="middleName" id="middleName" :value="old('middleName')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('middleName')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span id="lastnameLabel">Last name<x-required-span /></x-input-span>
                                    <x-form-input name="lastName" id="lastname" :value="old('lastName')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('lastName')" class="mt-1" />
                            </div>
                            <!-- second row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="dateOfBirthLabel">Date of Birth<x-required-span /></x-input-span>
                                    <x-form-input type="date" name="dateOfBirth" id="dateOfBirth" :value="old('dateOfBirth')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('dateOfBirth')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span id="dateOfBirthLabel">Sex<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md sex" aria-label="sex" name="sex" :value="old('sex')">
                                        <option value="">Select</option>
                                        <option value="Female">Female</option>
                                        <option value="Male">Male</option>
                                    </select>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('sex')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span id="maritalStatusLabel">Marital Status<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md" aria-label="marital-status"
                                        name="maritalStatus" :value="old('maritalStatus')">
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
                                    <x-form-input type="tel" name="phone" id="phone" :value="old('phone')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('phone')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span>Address</x-input-span>
                                    <x-form-input name="address" :value="old('address')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('address')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span>State Residence<x-required-span /></x-input-span>
                                    <x-select-states name="stateResidence" :value="old('stateResidence')"/>
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
                                    <x-form-input type="email" name="email" id="email" :value="old('email')" placeholder="akpan12@gmail.com" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >Nationality<x-required-span /></x-input-span>
                                    <x-form-input name="nationality"  :value="old('nationality')" placeholder="eg. Nigerian" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('nationality')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >State Origin</x-input-span>
                                    <x-select-states name="stateOrigin" :value="old('stateOrigin')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('stateOrigin')" class="mt-1" />
                            </div>
                            <!-- second row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span >Occupation<x-required-span /></x-input-span>
                                    <x-form-input name="occupation" :value="old('occupation')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('occupation')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >Religion</x-input-span>
                                    <x-form-input name="religion" :value="old('religion')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('religion')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >Ethnic Group</x-input-span>
                                    <x-form-input name="ethnicGroup" :value="old('ethnicGroup')"/>
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
                                    <x-form-input name="nextOfKin" :value="old('nextOfKin')" />
                                </x-form-div>
                                <x-input-error :messages="$errors->get('nokName')" class="mt-1" />

                                <x-form-div>
                                    <x-input-span >Phone Number</x-input-span>
                                    <x-form-input type="tel" name="nextOfKinPhone" :value="old('nextOfKinPhone')"/>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('nextOfKinPhone')" class="mt-1" />
                                <x-form-div>
                                    <x-input-span >Relationship</x-input-span>
                                    <x-select-nok name="nextOfKinRship" :value="old('nextOfKinRship')"></x-select-nok>
                                </x-form-div>
                                <x-input-error :messages="$errors->get('nextOfKinRship')" class="mt-1" />
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-semibold">Previously Known Clinical Info</span>
                            <!-- first row -->
                            <div class="row">
                                @include("patients.partials.known-clinical-info", ["disabled" => false  ])
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
