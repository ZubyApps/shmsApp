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
                                <x-input-span id="hmsStockLevelLabel">HMS Stocklevel<x-required-span /></x-input-span>
                                <x-form-input type="number" name="hmsStock" id="hmsStock" readonly/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="actualStockLevelLabel">Actual Stocklevel<x-required-span /></x-input-span>
                                <x-form-input type="number" name="actualStock" id="actualStock" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="differenceLabel">Difference<x-required-span /></x-input-span>
                                <x-form-input type="number" name="difference" id="difference" readonly/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="newQuantityLabel">New Quantity<x-required-span /></x-input-span>
                                <x-form-input type="number" name="quantity" id="quantity" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="finalQuantityLabel">Final Quantity<x-required-span /></x-input-span>
                                <x-form-input type="number" name="finalQuantity" id="finalQuantity" readonly/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="finalStockLabel">Final Stock<x-required-span /></x-input-span>
                                <x-form-input type="number" name="finalStock" id="finalStock" readonly/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="commentLabel">Comment<x-required-span /></x-input-span>
                                <x-form-input type="text" name="comment" id="comment"/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="unitOfPurchaseLabel">Unit of Purchase<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" name="unitPurchase" id="unitPurchase">
                                    <option value="">Select</option>   
                                    @foreach ($unitDescriptions as $unitDescription )
                                        <option value="{{ $unitDescription->id}}" name="{{ $unitDescription->short_name }}">{{ $unitDescription->long_name .' ('.$unitDescription->short_name.')'}}</option>
                                    @endforeach
                                </select>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="purchasePriceLabel">Purchase Unit Price<x-required-span /></x-input-span>
                                <x-form-input type="number" name="purchasePrice" id="purchasePrice" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="sellingPriceLabel">Selling Unit Price<x-required-span /></x-input-span>
                                <x-form-input type="number" name="sellingPrice" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="expiryDateLabel">Expiry Date</x-input-span>
                                <x-form-input type="month" name="expiryDate" value="" />
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
