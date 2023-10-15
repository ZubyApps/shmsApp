@extends('layout')

@section('content')


<div class="mb-2 d-flex justify-content-center">
    <!-- first row -->
    <div class="container form-control p-3">
        <form action="">
            <x-form-label>Sign Up New Staff</x-form-label>
            <div class="row">
                <x-form-div class="col-xl-6">
                    <x-input-span>First Name<x-required-span /></x-input-span>
                    <x-form-input name="sponsorName" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Middle Name<x-required-span /></x-input-span>
                    <x-form-input name="sponsorName" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Last Name<x-required-span /></x-input-span>
                    <x-form-input name="sponsorName" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Phone No.<x-required-span /></x-input-span>
                    <x-form-input name="phoneNumber" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Email</x-input-span>
                    <x-form-input name="email" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Address</x-input-span>
                    <x-form-input name="address" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Highest Qaulification</x-input-span>
                    <x-form-input name="highestQualification" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Date Of Birth</x-input-span>
                    <x-form-input name="age" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Sex</x-input-span>
                    <x-form-input name="sex" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span id="maritalStatusLabel">Marital Status<x-required-span /></x-input-span>
                    <select class="form-select form-select-md" name="maritalStatus">
                        <option value="">Select Class</option>
                        <option value="Cash">Single</option>
                        <option value="Credit">Married</option>
                        <option value="Credit">Separated</option>
                        <option value="Credit">Divorced</option>
                        <option value="Credit">Widow</option>
                        <option value="Credit">Widower</option>
                    </select>
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-select-states>State of Origin</x-select-states>
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Department</x-input-span>
                    <x-form-input name="department" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Password</x-input-span>
                    <x-form-input type="password" name="password" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span>Confirm Password</x-input-span>
                    <x-form-input type="password" name="confirmPassword" />
                </x-form-div>
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