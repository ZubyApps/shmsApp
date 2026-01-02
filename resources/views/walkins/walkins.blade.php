@extends('layout')

@section('content')
@vite(['resources/js/walkIns.js'])

@include('walkins.walkInModal', ['title' => 'New WalkIn', 'isUpdate' => false, 'id' => 'newWalkInModal'])
@include('walkins.walkInModal', ['title' => 'Update WalkIn', 'isUpdate' => true, 'id' => 'updateWalkInModal'])
@include('walkins.walkInPrescriptionModal', ['title' => 'Add Request for WalkIn', 'isUpdate' => false, 'id' => 'walkInPrescriptionsModal'])
@include('walkins.addResultModal', ['title' => 'Add Result', 'isUpdate' => false, 'id' => 'addResultModal'])
@include('walkins.addResultModal', ['title' => 'Update Result', 'isUpdate' => true, 'id' => 'updateResultModal'])
@include('walkins.payWalkInModal', ['title' => 'Make Payment','id' => 'payWalkInModal'])
@include('extras.labResultModal', ['title' => 'Lab Result', 'dept' => 'Lab', 'isPharmacy' => false, 'id' => 'labResultModal'])
@include('billing.posBillModal', ['title' => "", 'isPos' => true, 'isWalkIn' => true, 'id' => 'posBillModal'])
@include('walkins.linkToVisitModal', ['title' => "Link Walkin to the Patient's Visit",'id' => 'linkToVisitModal'])


    <div class="container p-1 mt-5 bg-white">
        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-listOfWalkIns-tab" data-bs-toggle="tab" data-bs-target="#nav-listOfWalkIns" 
                    type="button" role="tab" aria-controls="nav-listOfWalkIns" aria-selected="true">List of WalkIns</button>

                    {{-- <button class="nav-link" id="nav-thirdParties-tab" data-bs-toggle="tab" data-bs-target="#nav-thirdParties"
                        type="button" role="tab" aria-controls="nav-thirdParties" aria-selected="false">Third Parties</button> --}}
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- List of Third party serrvices table -->
                <div class="tab-pane fade show active" id="nav-listOfWalkIns" role="tabpanel"
                    aria-labelledby="nav-listOfWalkIns-tab" tabindex="0">
                    <div class="py-4">
                        <div class="text-start py-3">
                            <button type="button" id="newWalkInBtn" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-1"></i>
                                New WalkIn
                            </button>
                        </div>
                        <table id="walkInsTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Names</th>
                                    <th>Age</th>
                                    <th>Sex</th>
                                    <th>Phone</th>
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