@extends('layout')

@section('content')


<div class="mb-2 d-flex justify-content-center">
    <!-- first row -->
    <div class="container form-control p-3">
        <form method="POST" action="{{ route('register') }}">
            @csrf

            <x-form-label>Sign Up New Staff</x-form-label>
            <div class="row">
                <x-form-div class="">
                    <x-input-span>First Name<x-required-span /></x-input-span>
                    <x-form-input name="firstName" :value="old('firstName')"/>
                </x-form-div>
                    <x-input-error :messages="$errors->get('firstName')" class="" />
                

                <x-form-div class="">
                    <x-input-span>Middle Name<x-required-span /></x-input-span>
                    <x-form-input name="middleName" :value="old('middleName')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('middleName')" class="" />

                <x-form-div class="">
                    <x-input-span>Last Name<x-required-span /></x-input-span>
                    <x-form-input name="lastName" :value="old('lastName')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('lastName')" class="" />
            </div>

            <div class="row">
                <x-form-div class="">
                    <x-input-span>Username<x-required-span /></x-input-span>
                    <x-form-input name="username" :value="old('username')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('username')" class="" />

                <x-form-div class="">
                    <x-input-span>Phone No.<x-required-span /></x-input-span>
                    <x-form-input type="number" name="phone_no" :value="old('phone_no')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('phone_no')" class="" />

                <x-form-div class="">
                    <x-input-span>Email</x-input-span>
                    <x-form-input name="email" autocomplete="username" :value="old('email')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('email')" class="" />
            </div>

            <div class="row">
                <x-form-div class="">
                    <x-input-span>Address</x-input-span>
                    <x-form-textarea name="address" :value="old('address')" rows="1"></x-form-textarea>
                </x-form-div>
                <x-input-error :messages="$errors->get('address')" class="" />

                <x-form-div class="">
                    <x-input-span>Highest Qaulification</x-input-span>
                    <x-form-input name="highestQualification" :value="old('highestQualification')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('highestQualification')" class="" />

                <x-form-div class="">
                    <x-input-span>Date Of Birth</x-input-span>
                    <x-form-input type="date" name="dateOfBirth" :value="old('dateOfBirth')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('dateOfBirth')" class="" />
            </div>

            <div class="row">
                <x-form-div class="">
                    <x-input-span>Sex</x-input-span>
                    <select class="form-select form-select-md" name="sex" :value="old('sex')">
                        <option value="">Select Sex</option>
                        <option value="female">Female</option>
                        <option value="male">Male</option>
                    </select>
                </x-form-div>
                <x-input-error :messages="$errors->get('sex')" class="" />

                <x-form-div class="">
                    <x-input-span id="maritalStatusLabel">Marital Status<x-required-span /></x-input-span>
                    <select class="form-select form-select-md" name="maritalStatus" :value="old('maritalStatus')">
                        <option value="">Select Class</option>
                        <option value="Single">Single</option>
                        <option value="Married">Married</option>
                        <option value="Separated">Separated</option>
                        <option value="Divorced">Divorced</option>
                        <option value="Widow">Widow</option>
                        <option value="Widower">Widower</option>
                    </select>
                </x-form-div>
                <x-input-error :messages="$errors->get('maritalStatus')" class="" />

                <x-form-div class="">
                    <x-input-span>State of Origin</x-input-span>
                    <x-select-states name="stateOfOrigin" :value="old('stateOfOrigin')"></x-select-states>
                </x-form-div>
                <x-input-error :messages="$errors->get('stateOfOrigin')" class="" />
            </div>

            <div class="row">
                <x-form-div class="">
                    <x-input-span>Next of Kin</x-input-span>
                    <x-form-input name="nextOfKin" :value="old('nextOfKin')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('nextOfKin')" class="" />

                <x-form-div class="">
                    <x-input-span>Next of Kin Relationship</x-input-span>
                    <x-select-nok name="nextOfKinRship" :value="old('nextOfKinRship')"></x-select-nok>
                </x-form-div>
                <x-input-error :messages="$errors->get('nextOfKinRship')" class="" />

                <x-form-div class="">
                    <x-input-span>Next of Kin Phone</x-input-span>
                    <x-form-input type="number" name="nextOfKinPhone" :value="old('nextOfKinPhone')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('nextOfKinPhone')" class="" />
            </div>

            <div class="row">
                <x-form-div class="">
                    <x-input-span>Date of Employment</x-input-span>
                    <x-form-input type="date" name="dateOfEmployment" :value="old('dateOfEmployment')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('dateOfEmployment')" class="" />

                <x-form-div class="">
                    <x-input-span>Date Of Exit</x-input-span>
                    <x-form-input type="date" name="dateOfExit" :value="old('dateOfExit')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('dateOfExit')" class="" />
                    
                <x-form-div class="">
                    <x-input-span>Department</x-input-span>
                    <x-form-input name="department" :value="old('department')"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('department')" class="" />
            </div>

            <div class="row">
                <x-form-div class="">
                    <x-input-span>Password</x-input-span>
                    <x-form-input type="password" name="password" autocomplete="new-password"/>
                </x-form-div>
                <x-input-error :messages="$errors->get('password')" class="" />

                <x-form-div class="">
                    <x-input-span>Confirm Password</x-input-span>
                    <x-form-input type="password" name="password_confirmation" />
                </x-form-div>
                <x-input-error :messages="$errors->get('password_confirmation')" class="" />
            </div>
            <div class="modal-footer mt-3">
                <button type="submit" id="createBtn" class="btn bg-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    Sign Up
                </button>
            </div>
        </form>
    </div>
</div>

@endsection