<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">                 
                    <div class="">
                        <div class="mb-2">
                            <x-form-span>Bio</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span id="patientLabel">Patient</x-input-span>
                                    <x-form-input name="patientIds" readonly value="Patrick Abiodun Aso"/>
                                </x-form-div>
                                <x-form-div class="sponsorNameDiv">
                                    <x-input-span id="sponsorNameLabel">Sponsor Name</x-input-span>
                                    <x-form-input type="search" class="sponsorName" name="sponsorName" value="Axe Mansard" readonly/>
                                </x-form-div>
                                <x-form-div class="">
                                    <x-input-span>Age</x-input-span>
                                    <x-form-input name="age" class="age" value="49" readonly />
                                </x-form-div>
                                <x-form-div class="">
                                    <x-input-span>Sex</x-input-span>
                                    <x-form-input name="sex" class="Male" value="Male" readonly />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="maritalStatusLabel">Marital Status</x-input-span>
                                    <x-form-input name="sex" class="Male" value="Male" readonly />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span>Phone Number</x-input-span>
                                    <x-form-input type="tel" name="phoneNumber" id="phoneNumber" value="08034987761" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Ethnic Group</x-input-span>
                                    <x-form-input name="ethnicGroup" value="Yoruba" diabled/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-semibold">Previously Known Clinical Info</span>
                            <div class="row">
                                @include("patients.partials.known-clinical-info", ["readonly" => true])
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
                                <x-form-div>
                                    <x-input-span id="middleNameLabel">Middle Name</x-input-span>
                                    <x-form-input name="middleName" id="middleName" />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="surnameLabel">Surname<x-required-span /></x-input-span>
                                    <x-form-input name="surname" id="surname" />
                                </x-form-div>
                            </div>
                            <!-- second row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="dateOfBirthLabel">Date of Birth<x-required-span /></x-input-span>
                                    <x-form-input type="date" name="dateOfBirth" id="dateOfBirth" />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="dateOfBirthLabel">Sex<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md sex" aria-label="sex" name="sex">
                                        <option value="">Select</option>
                                        <option value="Female">Female</option>
                                        <option value="Male">Male</option>
                                    </select>
                                </x-form-div>
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
                            </div>

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
