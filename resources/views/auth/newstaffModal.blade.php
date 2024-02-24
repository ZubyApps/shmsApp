<div class="container">
    <div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">              
                    <!-- first row -->
                    <div class="container form-control">
                        <x-form-label>Sign Up New Staff</x-form-label>
                        <div class="row">
                            <x-form-div class="">
                                <x-input-span>First Name<x-required-span /></x-input-span>
                                <x-form-input name="firstName{{ $isUpdate ? 1 : '' }}" id="firstName{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>
                            
                            <x-form-div class="">
                                <x-input-span>Middle Name<x-required-span /></x-input-span>
                                <x-form-input name="middleName{{ $isUpdate ? 1 : '' }}" id="middleName{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>

                            <x-form-div class="">
                                <x-input-span>Last Name<x-required-span /></x-input-span>
                                <x-form-input name="lastName{{ $isUpdate ? 1 : '' }}" id="lastName{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>
                        </div>

                        <div class="row">
                            <x-form-div>
                                <x-input-span>Username<x-required-span /></x-input-span>
                                <x-form-input name="username{{ $isUpdate ? 1 : '' }}" id="username{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>

                            <x-form-div>
                                <x-input-span>Phone No.<x-required-span /></x-input-span>
                                <x-form-input type="number" name="phoneNumber{{ $isUpdate ? 1 : '' }}" id="phoneNumber{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>

                            <x-form-div>
                                <x-input-span>Email</x-input-span>
                                <x-form-input name="email{{ $isUpdate ? 1 : '' }}" autocomplete="username" id="email{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>
                        {{-- </div>

                        <div class="row"> --}}
                            <x-form-div>
                                <x-input-span>Address<x-required-span /></x-input-span>
                                <x-form-textarea name="address{{ $isUpdate ? 1 : '' }}" id="address{{ $isUpdate ? 1 : '' }}" rows="1"></x-form-textarea>
                            </x-form-div>

                            <x-form-div>
                                <x-input-span>Highest Qaulification<x-required-span /></x-input-span>
                                <x-form-input name="highestQualification{{ $isUpdate ? 1 : '' }}" id="highestQualification{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>

                            <x-form-div>
                                <x-input-span>Date Of Birth<x-required-span /></x-input-span>
                                <x-form-input type="date" name="dateOfBirth{{ $isUpdate ? 1 : '' }}" id="dateOfBirth{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>
                        {{-- </div> --}}

                        {{-- <div class="row"> --}}
                            <x-form-div>
                                <x-input-span>Sex<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" name="sex{{ $isUpdate ? 1 : '' }}" id="sex{{ $isUpdate ? 1 : '' }}">
                                    <option value="">Select Sex</option>
                                    <option value="female">Female</option>
                                    <option value="male">Male</option>
                                </select>
                            </x-form-div>

                            <x-form-div>
                                <x-input-span id="maritalStatusLabel">Marital Status<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" name="maritalStatus{{ $isUpdate ? 1 : '' }}" id="maritalStatus{{ $isUpdate ? 1 : '' }}">
                                    <option value="">Select Class</option>
                                    <option value="Single">Single</option>
                                    <option value="Married">Married</option>
                                    <option value="Separated">Separated</option>
                                    <option value="Divorced">Divorced</option>
                                    <option value="Widow">Widow</option>
                                    <option value="Widower">Widower</option>
                                </select>
                            </x-form-div>

                            <x-form-div>
                                <x-input-span>State of Origin<x-required-span /></x-input-span>
                                <x-select-states name="stateOfOrigin{{ $isUpdate ? 1 : '' }}" id="stateOfOrigin{{ $isUpdate ? 1 : '' }}"></x-select-states>
                            </x-form-div>
                        {{-- </div> --}}

                        {{-- <div class="row"> --}}
                            <x-form-div>
                                <x-input-span>Next of Kin<x-required-span /></x-input-span>
                                <x-form-input name="nextOfKin{{ $isUpdate ? 1 : '' }}" id="nextOfKin{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>

                            <x-form-div>
                                <x-input-span>Next of Kin Relationship<x-required-span /></x-input-span>
                                <x-select-nok name="nextOfKinRship{{ $isUpdate ? 1 : '' }}" id="nextOfKinRship{{ $isUpdate ? 1 : '' }}"></x-select-nok>
                            </x-form-div>

                            <x-form-div>
                                <x-input-span>Next of Kin Phone<x-required-span /></x-input-span>
                                <x-form-input type="number" name="nextOfKinPhone{{ $isUpdate ? 1 : '' }}" id="nextOfKinPhone{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>
                        {{-- </div> --}}

                        {{-- <div class="row"> --}}
                            <x-form-div>
                                <x-input-span>Date of Employment<x-required-span /></x-input-span>
                                <x-form-input type="datetime-local" name="dateOfEmployment{{ $isUpdate ? 1 : '' }}" id="dateOfEmployment{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>

                            <x-form-div>
                                <x-input-span>Date Of Exit</x-input-span>
                                <x-form-input type="datetime-local" name="dateOfExit{{ $isUpdate ? 1 : '' }}" id="dateOfExit{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>
                                
                            <x-form-div>
                                <x-input-span>Special Note</x-input-span>
                                <x-form-input name="specialNote{{ $isUpdate ? 1 : '' }}" id="specialNote{{ $isUpdate ? 1 : '' }}"/>
                            </x-form-div>
                        {{-- </div> --}}

                        {{-- <div class="row"> --}}
                            <x-form-div class="m-2 form-switch hover changePasswordDiv {{ $isUpdate ? '' : 'd-none' }}">
                                <input class="form-check-input" id="changePasswordRadioBtn" type="checkbox" role="switch" name="changePassword" value="" id="changePassword">
                                <label class="form-check-label ms-1" for="changePassword">
                                    Change Password?
                                </label>
                            </x-form-div>
                            <div class="passwordDiv {{ $isUpdate ? 'd-none' : '' }} row p-0 m-0">
                                <x-form-div>
                                    <x-input-span>Password<x-required-span /></x-input-span>
                                    <x-form-input type="password" name="password" autocomplete="new-password"/>
                                </x-form-div>
    
                                <x-form-div>
                                    <x-input-span>Confirm Password<x-required-span /></x-input-span>
                                    <x-form-input type="password" name="password_confirmation" />
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
                    <button type="button" id="{{ $isUpdate ? 'saveStaffBtn' : 'registerStaffBtn' }}" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $isUpdate ? 'Update' : 'Register' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
