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
                                <x-input-span id="resourceLabel">Company<x-required-span /></x-input-span>
                                <x-form-input type="text" class="company" name="company"/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="reOrderLabel">Contact Person</x-input-span>
                                <x-form-input type="text" name="person"/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="purchasePriceLabel">Phone</x-input-span>
                                <x-form-input type="number" name="phone" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="sellingPriceLabel">Email</x-input-span>
                                <x-form-input type="email" name="email" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="expiryDateLabel">Address</x-input-span>
                                <x-form-input type="text" name="address" value="" />
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
                <button type="button" id="{{ $isUpdate ? 'saveResourceSupplier' : 'createResourceSupplier' }}Btn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Save' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
