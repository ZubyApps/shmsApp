@extends('layout')

@section('content')
@vite(['resources/js/adminSettings.js'])

@include('admin.modals.stockDateModal', ['title' => 'Create Stock Date', 'isUpdate' => false, 'id' => 'newResourceStockDateModal'])
@include('admin.modals.stockDateModal', ['title' => 'Update Stock Date', 'isUpdate' => true, 'id' => 'updateResourceStockDateModal'])
{{-- @include('hmo.treatmentDetailsModal', ['title' => 'Treatment Details', 'isUpdate' => false, 'id' => 'treatmentDetailsModal'])
@include('hmo.approvalModal', ['title' => 'Approve Medication/Treatment', 'isUpdate' => false, 'id' => 'approvalModal']) --}}
@include('admin.modals.sponsorCategoryModal', ['title' => 'New Sponsor Category', 'isUpdate' => false, 'id' => 'newSponsorCategoryModal'])
@include('admin.modals.sponsorCategoryModal', ['title' => 'Edit Sponsor Category', 'isUpdate' => true, 'id' => 'updateSponsorCategoryModal'])
@include('admin.modals.resourceCategoryModal', ['title' => 'New Resource Category', 'isUpdate' => false, 'id' => 'newResourceCategoryModal'])
@include('admin.modals.resourceCategoryModal', ['title' => 'Edit Resource Category', 'isUpdate' => true, 'id' => 'updateResourceCategoryModal'])

    <div class="container mt-5 bg-white">
        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-sponsorCategory-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-sponsorCategory" type="button" role="tab" aria-controls="nav-sponsorCategory"
                        aria-selected="true">Sponsor Category</button>

                    <button class="nav-link" id="nav-resourceStockDate-tab" data-bs-toggle="tab" data-bs-target="#nav-resourceStockDate"
                        type="button" role="tab" aria-controls="nav-resourceStockDate" aria-selected="false">Resource Stock Date</button>

                    <button class="nav-link" id="nav-resourceCategory-tab" data-bs-toggle="tab" data-bs-target="#nav-resourceCategory"
                        type="button" role="tab" aria-controls="nav-resourceCategory" aria-selected="false">Resource Category</button>

                    {{-- <button class="nav-link" id="nav-reporst-tab" data-bs-toggle="tab" data-bs-target="#nav-reports"
                        type="button" role="tab" aria-controls="nav-reports" aria-selected="false">Reports</button> --}}
                </div>
            </nav>
            <div class="tab-content px-2" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-sponsorCategory" role="tabpanel"
                    aria-labelledby="nav-sponsorCategory-tab" tabindex="0">
                    
                    <div class="text-start my-4">
                        <button class="btn btn-primary" type="button" id="addSponsnorCategoryBtn">
                            <i class="bi bi-plus-circle"></i>
                            Category
                        </button>
                    </div>

                    <div class="container">
                        <table id="sponsorCategoryTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Descrption</th>
                                    <th>Consultation</th>
                                    <th>Pay Class</th>
                                    <th>Approval</th>
                                    <th>Bill Matrix</th>
                                    <th>Pay Bal?</th>
                                    <th>Created</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- resourceStockDate table -->
                <div class="tab-pane fade" id="nav-resourceStockDate" role="tabpanel" aria-labelledby="nav-resourceStockDate-tab"
                    tabindex="0">
                    
                    <div class="text-start gx-5 my-4">
                        <button class="btn btn-primary" type="button" id="addResourceStockDateBtn">
                            <i class="bi bi-plus-circle"></i>
                            Date
                        </button>
                    </div>

                    <div class="container">
                        <table id="resourceStockDateTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Description</th>
                                    <th>Participants</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- resourceCategory table -->
                <div class="tab-pane fade" id="nav-resourceCategory" role="tabpanel" aria-labelledby="nav-resourceCategory-tab"
                    tabindex="0">

                    <div class="text-start my-4">
                        <button class="btn btn-primary" type="button" id="addResourceCategoryBtn">
                            <i class="bi bi-plus-circle"></i>
                            Category
                        </button>
                    </div>
                    <div class="container">
                        <table id="resourceCategoryTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    {{-- <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                   
                </div>
                <!-- reports table -->
                {{-- <div class="tab-pane fade" id="nav-reports" role="tabpanel" aria-labelledby="nav-reports-tab"
                    tabindex="0">
                    <div class="py-4 justify-content-center">
                        <table id="reportsTable" class="table table-hover align-center table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Sponsor</th>
                                    <th>Doctor</th>
                                    <th>Diagnosis</th>
                                    <th>Status</th>
                                    <th>Verified</th>
                                    <th>Treatment</th>
                                    <th>Bill-Sent</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>09/10/2023</td>
                                    <td>SH21/4012 Josephine Ene Ode</td>
                                    <td>Axe Mansard</td>
                                    <td>Dr Toby</td>
                                    <td>F12Z-Acute Spundolosis</td>
                                    <td>Out-Patient</td>
                                    <td class="fst-italic">Pending</td>
                                    <td class="fst-italic">No Code</td>
                                    <td class="fst-italic">Not Sent</td>
                                    <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Sent</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>08/10/2023</td>
                                    <td>SH21/1403 Shine Ewara</td>
                                    <td>Health Partners</td>
                                    <td>Dr Tony</td>
                                    <td>F12Z-Severe Malaria</td>
                                    <td>In-Patient</td>
                                    <td class="fst-italic">Verified</td>
                                    <td class="fst-italic">HP-45srt6if1</td>
                                    <td class="fst-italic">Sent</td>
                                    <td>
                                        <button class="btn btn-outline-primary" id="treatmentDetailsBtn">Sent</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div> --}}
            </div>
        </div>
    </div>
@endsection
