@extends('layout')

@section('content')
@vite(['resources/js/thirdPartyServices.js'])

@include('thirdpartyservices.thirdPartyModal', ['title' => 'New Third Party', 'isUpdate' => false, 'id' => 'newthirdPartyModal'])
@include('thirdpartyservices.thirdPartyModal', ['title' => 'New Third Party', 'isUpdate' => true, 'id' => 'updatethirdPartyModal'])


    <div class="container p-1 mt-5 bg-white">
        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-listOfServices-tab" data-bs-toggle="tab" data-bs-target="#nav-listOfServices" 
                    type="button" role="tab" aria-controls="nav-listOfServices" aria-selected="true">List of Services</button>

                    <button class="nav-link" id="nav-thirdParties-tab" data-bs-toggle="tab" data-bs-target="#nav-thirdParties"
                        type="button" role="tab" aria-controls="nav-thirdParties" aria-selected="false">Third Parties</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- List of Third party serrvices table -->
                <div class="tab-pane fade show active" id="nav-listOfServices" role="tabpanel"
                    aria-labelledby="nav-listOfServices-tab" tabindex="0">
                    <div class="py-5">
                        <table id="listOfServicesTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>By</th>
                                    <th>Thrid Party</th>
                                    <th>Service</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Sponsor</th>
                                    <th>Status</th>
                                    <th>HMS Bill</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="">
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold">Total</td>
                                    <td class="fw-semibold"></td>
                                    <td class           ="fw-semibold"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- Third Party table -->
                <div class="tab-pane fade" id="nav-thirdParties" role="tabpanel" aria-labelledby="nav-thirdParties-tab" tabindex="0">
                    <div class="py-4">
                        <div class="text-start py-3">
                            <button type="button" id="newThirdPartyBtn" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Third Party
                            </button>
                        </div>
                        <table id="thirdPartiesTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Short Name</th>
                                    <th>Phone</th>
                                    <th>Address</th>
                                    <th>Email</th>
                                    <th>Comment</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection