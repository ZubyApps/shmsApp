@extends('layout')

@section('content')
@vite(['resources/js/adminSettings.js'])

@include('admin.modals.stockDateModal', ['title' => 'Create Stock Date', 'isUpdate' => false, 'id' => 'newResourceStockDateModal'])
@include('admin.modals.stockDateModal', ['title' => 'Update Stock Date', 'isUpdate' => true, 'id' => 'updateResourceStockDateModal'])
@include('admin.modals.sponsorCategoryModal', ['title' => 'New Sponsor Category', 'isUpdate' => false, 'id' => 'newSponsorCategoryModal'])
@include('admin.modals.sponsorCategoryModal', ['title' => 'Edit Sponsor Category', 'isUpdate' => true, 'id' => 'updateSponsorCategoryModal'])
@include('admin.modals.resourceCategoryModal', ['title' => 'New Resource Category', 'isUpdate' => false, 'id' => 'newResourceCategoryModal'])
@include('admin.modals.resourceCategoryModal', ['title' => 'Edit Resource Category', 'isUpdate' => true, 'id' => 'updateResourceCategoryModal'])
@include('admin.modals.expenseCategoryModal', ['title' => 'New Expense Category', 'isUpdate' => false, 'id' => 'newExpenseCategoryModal'])
@include('admin.modals.expenseCategoryModal', ['title' => 'Edit Expense Category', 'isUpdate' => true, 'id' => 'updateExpenseCategoryModal'])
@include('admin.modals.medicationCategoryModal', ['title' => 'New Medication Category', 'isUpdate' => false, 'id' => 'newMedicationCategoryModal'])
@include('admin.modals.medicationCategoryModal', ['title' => 'Edit Medication Category', 'isUpdate' => true, 'id' => 'updateMedicationCategoryModal'])
@include('admin.modals.payMethodModal', ['title' => 'New Pay Method', 'isUpdate' => false, 'id' => 'newPayMethodModal'])
@include('admin.modals.payMethodModal', ['title' => 'Edit Pay Method', 'isUpdate' => true, 'id' => 'editPayMethodModal'])

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
                        
                    <button class="nav-link" id="nav-payMethod-tab" data-bs-toggle="tab" data-bs-target="#nav-payMethod"
                    type="button" role="tab" aria-controls="nav-payMethod" aria-selected="false">Pay Methods</button>

                    <button class="nav-link" id="nav-expenseCategory-tab" data-bs-toggle="tab" data-bs-target="#nav-expenseCategory"
                        type="button" role="tab" aria-controls="nav-expenseCategory" aria-selected="false">Expense Category</button>

                    <button class="nav-link" id="nav-medicationCategory-tab" data-bs-toggle="tab" data-bs-target="#nav-medicationCategory"
                        type="button" role="tab" aria-controls="nav-medicationCategory" aria-selected="false">Medication Category</button>
                    </div>
            </nav>
            <div class="tab-content px-2" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane fade show active" id="nav-sponsorCategory" role="tabpanel"
                    aria-labelledby="nav-sponsorCategory-tab" tabindex="0">
                    
                    <div class="text-start my-4">
                        {{-- <button class="btn btn-primary" type="button" id="addSponsnorCategoryBtn">
                            <i class="bi bi-plus-circle"></i>
                            Category
                        </button> --}}
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
                        {{-- <button class="btn btn-primary" type="button" id="addResourceCategoryBtn">
                            <i class="bi bi-plus-circle"></i>
                            Category
                        </button> --}}
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
                <!-- pay method table -->
                <div class="tab-pane fade" id="nav-payMethod" role="tabpanel" aria-labelledby="nav-payMethod-tab"
                    tabindex="0">

                    <div class="text-start my-4">
                        <button class="btn btn-primary" type="button" id="addPayMethodBtn">
                            <i class="bi bi-plus-circle"></i>
                            Pay Method
                        </button>
                    </div>
                    <div class="container">
                        <table id="payMethodTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- expenseCategory table -->
                <div class="tab-pane fade" id="nav-expenseCategory" role="tabpanel" aria-labelledby="nav-expenseCategory-tab"
                    tabindex="0">

                    <div class="text-start my-4">
                        <button class="btn btn-primary" type="button" id="addExpenseCategoryBtn">
                            <i class="bi bi-plus-circle"></i>
                            Category
                        </button>
                    </div>
                    <div class="container">
                        <table id="expenseCategoryTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                   
                </div>
                <!-- medicationCategory table -->
                <div class="tab-pane fade" id="nav-medicationCategory" role="tabpanel" aria-labelledby="nav-medicationCategory-tab"
                    tabindex="0">

                    <div class="text-start my-4">
                        <button class="btn btn-primary" type="button" id="addMedicationCategoryBtn">
                            <i class="bi bi-plus-circle"></i>
                            Medication Category
                        </button>
                    </div>
                    <div class="container">
                        <table id="medicationCategoryTable" class="table table-hover align-middle table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
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
