<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <div class="form-control mb-2">
                        <div class="row">
                            <x-form-div class="col-xl-12">
                                <x-input-span id="subCategoryLabel">Resource<x-required-span /></x-input-span>
                                <x-form-input type="search" name="resource" class="resourceName resource" id="{{ $isUpdate ? 'updateResource' : 'newResource' }}" placeholder="Search..." list="{{ $isUpdate ? 'updateResourceList' : 'newResourceList' }}"/>
                                <datalist name="resource" type="text" class="decoration-none bg-white subCategoryList" id="{{ $isUpdate ? 'updateResourceList' : 'newResourceList' }}"></datalist>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="reOrderLabel">Quantity</x-input-span>
                                <x-form-input type="number" name="qty" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="reOrderLabel">Unit of Purchase</x-input-span>
                                <x-form-input type="number" name="qty" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="purchasePriceLabel">Purchase Price</x-input-span>
                                <x-form-input type="number" name="purchasePrice" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="sellingPriceLabel">Selling Price</x-input-span>
                                <x-form-input type="number" name="sellingPrice" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="expiryDateLabel">Expiry Date</x-input-span>
                                <x-form-input type="date" name="expiryDate" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="subCategoryLabel">Supplier<x-required-span /></x-input-span>
                                <x-form-input type="search" name="supplier" class="supplierName supplier" id="{{ $isUpdate ? 'updateSupplier' : 'newSupplier' }}" placeholder="Search..." list="{{ $isUpdate ? 'updateSupplierList' : 'newSupplierList' }}"/>
                                <datalist name="supplier" type="text" class="decoration-none bg-white supplierList" id="{{ $isUpdate ? 'updateSupplierList' : 'newSupplierList' }}"></datalist>
                            </x-form-div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="{{ $isUpdate ? 'saveResource' : 'createResource' }}Btn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Save' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
