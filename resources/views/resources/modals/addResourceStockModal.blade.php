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
                                <x-input-span id="resourceLabel">Resource<x-required-span /></x-input-span>
                                <x-form-input type="text" class="resource" name="resource" value="" readonly/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="reOrderLabel">Quantity<x-required-span /></x-input-span>
                                <x-form-input type="number" name="quantity" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="reOrderLabel">Unit of Purchase<x-required-span /></x-input-span>
                                <x-select-unit-description name="unitPurchase" id="unitPurchase"></x-select-unit-description>                                
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="purchasePriceLabel">Purchase Unit Price</x-input-span>
                                <x-form-input type="number" name="purchasePrice" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="sellingPriceLabel">Selling Unit Price</x-input-span>
                                <x-form-input type="number" name="sellingPrice" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="expiryDateLabel">Expiry Date</x-input-span>
                                <x-form-input type="date" name="expiryDate" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="resourceSuplierLabel">Supplier</x-input-span>
                                <x-form-input type="search" name="supplier" class="supplierName supplier" id="{{ $isUpdate ? 'updateSupplierInput' : 'newSupplierInput' }}" placeholder="Search..." list="{{ $isUpdate ? 'updateSupplierList' : 'newSupplierList' }}"/>
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
                <button type="button" id="{{ $isUpdate ? 'saveAddResourceStock' : 'createAddResourceStock' }}Btn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Save' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
