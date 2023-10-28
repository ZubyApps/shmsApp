@extends('layout')

@section('content')
    @vite(['resources/css/home.scss', 'resources/js/app.js'])
    <div class="container">
        <main class="px-0">
            <div class="vh-50 p-5 mb-4 cover-image">
                <div class="col-lg-6 px-0 display-4">
                    <h1 class="fst-italic ">Welcome To <span class="fw-bold ">SHMS</span> </h1>
                    <p class="fs-5"><span class="fw-bold">S</span>andra <span class="fw-bold">H</span>ospital <span
                            class="fw-bold">M</span>anagement <span class="fw-bold">S</span>ystem</p>
                    <div class="">
                        <a href="/patients" class="btn btn-outline-primary mx-1"><i class="bi bi-people-fill"> Patients</i></a>
                        <a href="/doctors" class="btn btn-outline-primary mx-1"><i class="bi bi-lungs-fill" aria-hidden="true">Doctors</i></a>
                        <a href="/nurses" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-thermometer-low">Nurses</i></a>
                        <br>
                        <a href="/investigations" class="btn btn-outline-primary mx-1"><i class="bi bi-eyedropper" aria-hidden="true">Investigations</i></a>
                        <a href="/pharmacy" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-capsule">Pharmacy</i></a>
                        <a href="/hmodesk" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-calendar2-plus-fill">HMO</i></a>
                        <br>
                        <a href="/billing" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-receipt">Billing</i></a>
                        <a href="/resources" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-minecart-loaded">Resources</i></a>
                        <a href="/admin" class="btn btn-outline-primary fw- mx-1"><i class="bi bi-person-fill">Admin</i></a>
                    </div>
                    <br>
                    <p class="fs-5 mb-0"><a href="#" class="text-decoration-none text-black">The Hospital's Motto...</a></p>
                </div>
            </div>
        </main>
    </div>
@endsection
