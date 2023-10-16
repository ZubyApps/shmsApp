@extends('layout')

@section('content')
@vite(['resources/js/resources.js'])

    <div class="container p-1 mt-5">
        {{-- <div class="offcanvas offcanvas-start" data-bs-scroll="true" tabindex="-1" id="offcanvasWithBothOptions"
            aria-labelledby="offcanvasWithBothOptionsLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-primary" id="offcanvasWithBothOptionsLabel">List of Waiting Patients</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <div class="py-4 ">
                    <table id="waitingListTable" class="table table-hover align-middle table-sm bg-primary">
                        <thead>
                            <tr>
                                <th>Patient</th>
                                <th>Age</th>
                                <th>Sponsor</th>
                                <th>Consult</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>SH21/4012 Joesphine Ene Odeh</td>
                                <td>25</td>
                                <td>Alex Odeh Family</td>
                                <td><i class="btn btn-outline-none text-primary bi bi-clipboard-plus" id="newConsultationBtn" data-patientType="ANC"></i></td>
                            </tr>
                            <tr>
                                <td>SH23/7865 Patrick Abiodun Aso</td>
                                <td>32</td>
                                <td>Axe Mansard HMO</td>
                                <td><span class="badge rounded-pill text-bg-light text-secondary p-2" id="newConsultationBtn" data-patientType="Regular">Dr Toby</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div> --}}

        <div class="text-start mb-4">
            <button class="btn btn-primary text-white" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#offcanvasWithBothOptions" aria-controls="offcanvasWithBothOptions">
                <i class="bi bi-plus-circle"></i>
                Resource
            </button>
        </div>

    </div>

@endsection