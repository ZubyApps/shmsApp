<div class="container">
    <div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">              
                <div class="mb-2 d-flex justify-content-center">
                    <!-- first row -->
                    <div class="container form-control p-3">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <x-form-label>Sign Up New Staff</x-form-label>
                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span>First Name<x-required-span /></x-input-span>
                                    <x-form-input name="firstName" id="firstName"/>
                                </x-form-div>
                                
                                <x-form-div class="">
                                    <x-input-span>Middle Name<x-required-span /></x-input-span>
                                    <x-form-input name="middleName" id="middleName"/>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>Last Name<x-required-span /></x-input-span>
                                    <x-form-input name="lastName" id="lastName"/>
                                </x-form-div>
                            </div>

                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span>Username<x-required-span /></x-input-span>
                                    <x-form-input name="username" id="username"/>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>Phone No.<x-required-span /></x-input-span>
                                    <x-form-input type="number" name="phone_no" id="phone_no"/>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>Email</x-input-span>
                                    <x-form-input name="email" autocomplete="username" id="email"/>
                                </x-form-div>
                            </div>

                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span>Address</x-input-span>
                                    <x-form-textarea name="address" id="address" rows="1"></x-form-textarea>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>Highest Qaulification</x-input-span>
                                    <x-form-input name="highestQualification" id="highestQualification"/>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>Date Of Birth</x-input-span>
                                    <x-form-input type="date" name="dateOfBirth" id="dateOfBirth"/>
                                </x-form-div>
                            </div>

                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span>Sex</x-input-span>
                                    <select class="form-select form-select-md" name="sex" id="sex">
                                        <option value="">Select Sex</option>
                                        <option value="female">Female</option>
                                        <option value="male">Male</option>
                                    </select>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span id="maritalStatusLabel">Marital Status<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md" name="maritalStatus" id="maritalStatus">
                                        <option value="">Select Class</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Separated">Separated</option>
                                        <option value="Divorced">Divorced</option>
                                        <option value="Widow">Widow</option>
                                        <option value="Widower">Widower</option>
                                    </select>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>State of Origin</x-input-span>
                                    <x-select-states name="stateOfOrigin" id="stateOfOrigin"></x-select-states>
                                </x-form-div>
                            </div>

                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span>Next of Kin</x-input-span>
                                    <x-form-input name="nextOfKin" id="nextOfKin"/>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>Next of Kin Relationship</x-input-span>
                                    <x-select-nok name="nextOfKinRship" id="nextOfKinRship"></x-select-nok>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>Next of Kin Phone</x-input-span>
                                    <x-form-input type="number" name="nextOfKinPhone" id="nextOfKinPhone"/>
                                </x-form-div>
                            </div>

                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span>Date of Employment</x-input-span>
                                    <x-form-input type="date" name="dateOfEmployment" id="dateOfEmployment"/>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>Date Of Exit</x-input-span>
                                    <x-form-input type="date" name="dateOfExit" id="dateOfExit"/>
                                </x-form-div>
                                    
                                <x-form-div class="">
                                    <x-input-span>Department</x-input-span>
                                    <x-form-input name="department" id="department"/>
                                </x-form-div>
                            </div>

                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span>Password</x-input-span>
                                    <x-form-input type="password" name="password" autocomplete="new-password"/>
                                </x-form-div>

                                <x-form-div class="">
                                    <x-input-span>Confirm Password</x-input-span>
                                    <x-form-input type="password" name="password_confirmation" />
                                </x-form-div>
                            </div>
                        </form>
                    </div>
                </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="submit" id="createBtn" class="btn bg-primary text-white">
                        <i class="bi bi-check-circle me-1"></i>
                        Sign Up
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
