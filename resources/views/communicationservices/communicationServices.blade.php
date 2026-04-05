@extends('layout')

@section('content')
@vite(['resources/js/communications.js'])

@include('communicationservices.buyUnitsModal', ['title' => 'Place order for Units', 'id' => 'buyUnitsModal'])

    <div class="container mt-5 bg-white">
        <div>
            <div class="mb-5">
                <div class="row ">
                    <div class="col-xl-6" id="buyUnitsDiv">
                        <button class="btn btn-primary" id="buyUnitsBtn">Buy Units</button>
                    </div>
                    <div class="col-xl-6 text-end" id="unitsDiv">
                    </div>
                </div>
            </div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-listOfSentSms-tab" data-bs-toggle="tab" data-bs-target="#nav-listOfSentSms" 
                    type="button" role="tab" aria-controls="nav-listOfSentSms" aria-selected="true">SMS</button>

                    <button class="nav-link" id="nav-thirdParties-tab" data-bs-toggle="tab" data-bs-target="#nav-thirdParties"
                        type="button" role="tab" aria-controls="nav-thirdParties" aria-selected="false">WhatsApp</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- List of sent SMSes table -->
                <div class="tab-pane fade show active" id="nav-listOfSentSms" role="tabpanel"
                    aria-labelledby="nav-listOfSentSms-tab" tabindex="0">
                    <div class="py-4">
                        {{-- <div class="text-start py-3">
                            <button type="button" id="sendSmsBtn" class="btn btn-primary">
                                <i class="bi bi-send"></i>
                                Send SMS
                            </button>
                        </div> --}}
                        <table id="listOfSentSmsTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Recipient</th>
                                    <th>Network</th>
                                    <th>Phone</th>
                                    <th>Type</th>
                                    <th>Message Sent</th>
                                    <th>Unit</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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