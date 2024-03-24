@extends('layout')

@section('content')
    @vite(['resources/css/home.scss', 'resources/js/app.js'])
<?php 
    $staffD = Auth::user()->designation->designation; 
    $staffA = Auth::user()->designation->access_level; 
?>
    <div class="container">
        <main class="px-0">
            <div class="vh-50 p-5 mb-4 cover-image">
                <div class="col-lg-6 px-0 display-4">
                    <h1 class="fst-italic ">Welcome To <span class="fw-bold ">SHMS</span> </h1>
                    <p class="fs-5"><span class="fw-bold">S</span>andra <span class="fw-bold">H</span>ospital <span
                            class="fw-bold">M</span>anagement <span class="fw-bold">S</span>ystem</p>
                    <div class="">
                        <a href="{{ $staffD === 'Bill Officer' || $staffD === 'HMO Officer' || $staffD === 'Nurse ' || $staffD === 'Records Clerk' || $staffD === 'Doctor' || $staffA > 4 ? '/patients' : '' }}" class="btn btn-outline-primary mx-1"><i class="bi bi-people-fill"> Patients</i></a>
                        <a href="{{ $staffD === 'Doctor' || $staffA > 4 ? '/doctors' : '' }}" class="btn btn-outline-primary mx-1"><i class="bi bi-lungs-fill"> Doctors</i></a>
                        <a href="{{ $staffD === 'Doctor' || $staffD === 'Nurse' || $staffA > 4 ? '/nurses' : '' }}" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-thermometer-low"> Nurses</i></a>
                        <br>
                        <a href="{{ $staffD === 'Doctor' || $staffD === 'LabTech' || $staffD === 'HMO Officer' || $staffA > 4 ? '/investigations' : '' }}" class="btn btn-outline-primary mx-1"><i class="bi bi-eyedropper" aria-hidden="true"> Investigations</i></a>
                        <a href="{{ $staffD === 'Pharmacy Tech' || $staffA > 4 ? '/pharmacy' : '' }}" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-capsule"> Pharmacy</i></a>
                        <a href="{{ $staffD === 'HMO Officer' || $staffA > 4 ? '/hmo' : '' }}" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-calendar2-plus-fill"> HMO</i></a>
                        <br>
                        <a href="{{ $staffD === 'Bill Officer' || $staffA > 4 ? '/billing' : '' }}" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-receipt"> Billing</i></a>
                        <a href="{{ $staffA > 4 ? '/resources' : '' }}" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-minecart-loaded"> Resources</i></a>
                        <a href="{{ $staffA > 4 ? '/admin' : '' }}" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-person-fill"> Admin</i></a>
                        <br>
                        <a href="{{ $staffD === 'Bill Officer' || $staffD === 'HMO Officer' || $staffD === 'Records Clerk' || $staffA  > 4 ? '/thirdpartyservices' : '' }}" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-arrow-up-right-square-fill"> Third Party Services</i></a>
                    </div>
                    <br>
                    <p class="fs-5 mb-0"><a href="#" class="text-decoration-none text-black">We treat, <span class="text-primary fw-bold">God</span> heals...</a></p>
                </div>
            </div>
        </main>
    </div>
@endsection
