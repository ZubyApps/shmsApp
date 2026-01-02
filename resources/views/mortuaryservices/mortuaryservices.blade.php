@extends('layout')

@section('content')
@vite(['resources/js/mortuaryService.js'])

@include('mortuaryservices.addDeceasedModal', ['title' => 'New Deceased', 'isUpdate' => false, 'id' => 'addDeceasedModal'])
@include('mortuaryservices.addDeceasedModal', ['title' => 'Update Deceased', 'isUpdate' => true, 'id' => 'updateDeceasedModal'])
@include('mortuaryservices.addBillModal', ['title' => 'Add Bill', 'isUpdate' => false, 'id' => 'addBillModal'])
@include('mortuaryservices.payBillModal', ['title' => 'Make Payment','id' => 'payBillModal'])

    <div class="container p-1 mt-5 bg-white">
        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-listOfDeceased-tab" data-bs-toggle="tab" data-bs-target="#nav-listOfDeceased" 
                    type="button" role="tab" aria-controls="nav-listOfDeceased" aria-selected="true">List of Deceased</button>

                    {{-- <button class="nav-link" id="nav-thirdParties-tab" data-bs-toggle="tab" data-bs-target="#nav-thirdParties"
                        type="button" role="tab" aria-controls="nav-thirdParties" aria-selected="false">Third Parties</button> --}}
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- List of Deceased table -->
                <div class="tab-pane fade show active" id="nav-listOfDeceased" role="tabpanel"
                    aria-labelledby="nav-listOfDeceased-tab" tabindex="0">
                    <div class="py-4">
                        <div class="text-start py-3">
                            <button type="button" id="addDeceasedBtn" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                Add Deceased
                            </button>
                        </div>
                        <table id="deceasedTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Deceased</th>
                                    <th>Sex</th>
                                    <th>Depositor</th>
                                    <th>Phone</th>
                                    <th>Relatioship</th>
                                    <th>Date Deposited</th>
                                    <th>Days</th>
                                    <th>Date Collected</th>
                                    <th>CreatedBy</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            {{-- <tfoot>
                                <tr class="">
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                    <td class="fw-semibold"></td>
                                </tr>
                            </tfoot> --}}
                        </table>
                    </div>
                </div>
                <!-- Third Party table -->
                {{-- <div class="tab-pane fade" id="nav-thirdParties" role="tabpanel" aria-labelledby="nav-thirdParties-tab" tabindex="0">
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
                </div> --}}
            </div>
        </div>
    </div>

@endsection