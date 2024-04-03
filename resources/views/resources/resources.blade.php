@extends('layout')

@section('content')
@vite(['resources/js/resources.js'])

@include('resources.modals.resourceSubCategoryModal', ['title' => 'New Resource SubCategory', 'isUpdate' => false, 'id' => 'newResourceSubCategoryModal'])
@include('resources.modals.resourceSubCategoryModal', ['title' => 'Update Resource SubCategory', 'isUpdate' => true, 'id' => 'updateResourceSubCategoryModal'])

@include('resources.modals.resourceModal', ['title' => 'New Resource', 'isUpdate' => false, 'id' => 'newResourceModal'])
@include('resources.modals.resourceModal', ['title' => 'Update Resource', 'isUpdate' => true, 'id' => 'updateResourceModal'])

@include('resources.modals.addResourceStockModal', ['title' => 'Add Resource Stock', 'isUpdate' => false, 'id' => 'newAddResourceStockModal'])
@include('resources.modals.addResourceStockModal', ['title' => 'Update Resource Stock', 'isUpdate' => true, 'id' => 'updateAddResourceStockModal'])

@include('resources.modals.resourceSupplierModal', ['title' => 'Add Resource Supplier', 'isUpdate' => false, 'id' => 'newResourceSupplierModal'])
@include('resources.modals.resourceSupplierModal', ['title' => 'Update Resource Supplier', 'isUpdate' => true, 'id' => 'updateResourceSupplierModal'])

    <div class="container mt-5 bg-white">
        <div>
            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button class="nav-link active" id="nav-resources-tab" data-bs-toggle="tab"
                        data-bs-target="#nav-resources" type="button" role="tab" aria-controls="nav-resources"
                        aria-selected="true">Resources</button>

                    <button class="nav-link" id="nav-resourceSubCategory-tab" data-bs-toggle="tab" data-bs-target="#nav-resourceSubCategory"
                        type="button" role="tab" aria-controls="nav-resourceSubCategory" aria-selected="false">Resource SubCategory</button>

                    <button class="nav-link" id="nav-addResourceStock-tab" data-bs-toggle="tab" data-bs-target="#nav-addResourceStock"
                        type="button" role="tab" aria-controls="nav-addResourceStock" aria-selected="false">Add Resource</button>

                    <button class="nav-link" id="nav-resourceSupplier-tab" data-bs-toggle="tab" data-bs-target="#nav-resourceSupplier"
                        type="button" role="tab" aria-controls="nav-resourceSupplier" aria-selected="false">Resource Suppliers</button>
                </div>
            </nav>
            <div class="tab-content" id="nav-tabContent">
                <!-- patients table -->
                <div class="tab-pane show active" id="nav-resources" role="tabpanel"
                    aria-labelledby="nav-resources-tab" tabindex="0">
                    
                    <div class="text-start my-4">
                        <button class="btn btn-primary" type="button" id="addResourceBtn">
                            <i class="bi bi-plus-circle"></i>
                            Resource
                        </button>
                    </div>
                    <div class="">
                        <table id="resourceTable" class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Flag</th>
                                    <th>Category</th>
                                    <th>SubCategory</th>
                                    <th>Unit</th>
                                    <th>Purchase<br>Price</th>
                                    <th>Selling<br>Price</th>
                                    <th>Re-order</th>
                                    <th>Stock</th>
                                    <th>Expiry Date</th>
                                    <th>Expired</th>
                                    <th>CreatedBy</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>                        
                </div>
                <!-- resourceSubCategory table -->
                <div class="tab-pane fade" id="nav-resourceSubCategory" role="tabpanel" aria-labelledby="nav-resourceSubCategory-tab"
                    tabindex="0">
                    <div class="text-start my-4">
                        <button class="btn btn-primary" type="button" id="addResourceSubCategoryBtn">
                            <i class="bi bi-plus-circle"></i>
                            SubCategory
                        </button>
                    </div>
                    <div class="">
                        <table id="resourceSubCategoryTable" class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Category</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    {{-- <th>Actions</th> --}}
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- addResourceStock table -->
                <div class="tab-pane fade" id="nav-addResourceStock" role="tabpanel" aria-labelledby="nav-addResourceStock-tab"
                    tabindex="0">
                    <div class="my-4">
                        <table id="addResourceStockTable" class="table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Resource</th>
                                    <th>Qty</th>
                                    <th>Purchase Price</th>
                                    <th>Selling Price</th>
                                    <th>Expiry Date</th>
                                    <th>Supplier</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
                <!-- resourceSupplier table -->
                <div class="tab-pane fade" id="nav-resourceSupplier" role="tabpanel" aria-labelledby="nav-resourceSupplier-tab"
                    tabindex="0">

                    <div class="text-start my-4">
                        <button class="btn btn-primary" type="button" id="addResourceSupplierBtn">
                            <i class="bi bi-plus-circle"></i>
                            Supplier
                        </button>
                    </div>
                        <div class="">
                            <table id="resourceSupplierTable" class="table table-hover align-center table-sm">
                                <thead>
                                    <tr>
                                        <th>Company</th>
                                        <th>Person</th>
                                        <th>Phone</th>
                                        <th>Email</th>
                                        <th>Address</th>
                                        <th>Created by</th>
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
