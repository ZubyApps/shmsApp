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
                                <x-input-span id="nameLabel">Name<x-required-span /></x-input-span>
                                <x-form-input type="text" class="name" name="name" id="name" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="flagLabel">Flag Sponsor<x-required-span /></x-input-span>
                                <x-select-flags name="flag" id="{{ $isUpdate ? 'flagUpdate' : 'flag' }}"></x-select-flags>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="nameLabel">Marked For<x-required-span /></x-input-span>
                                <x-form-input type="text" class="markedFor" name="markedFor" id="markedFor" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span>Category<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" name="resourceCategory" id="{{ $isUpdate ? 'updateResourceCategory' : 'newResourceCategory' }}">
                                    <option value="">Select Category</option>   
                                    @foreach ($categories as $category )
                                        <option value="{{ $category->id}}" name="{{ $category->name }}">{{ $category->name}}</option>
                                    @endforeach
                                </select>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="subCategoryLabel">Sub-Category<x-required-span /></x-input-span>
                                <x-form-input type="search" name="resourceSubCategory" class="subCategoryName categorySponsor" id="{{ $isUpdate ? 'updateResourceSubCategory' : 'newResourceSubCategory' }}" placeholder="Search..." list="{{ $isUpdate ? 'updateSubCategoryList' : 'newSubCategoryList' }}"/>
                                <datalist name="resourceSubCategory" type="text" class="decoration-none bg-white subCategoryList" id="{{ $isUpdate ? 'updateSubCategoryList' : 'newSubCategoryList' }}"></datalist>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="reOrderLabel">Re-order Level<x-required-span /></x-input-span>
                                <x-form-input type="number" name="reOrder" id="reOrder" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="purchasePriceLabel">Purchase Price<x-required-span /></x-input-span>
                                <x-form-input type="number" name="purchasePrice" id="purchasePrice" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="sellingPriceLabel">Selling Price<x-required-span /></x-input-span>
                                <x-form-input type="number" name="sellingPrice" id="sellingPrice" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="unitLabel">Unit Description<x-required-span /></x-input-span>
                                <x-select-unit-description name="unitDescription" id="unitDescription"></x-select-unit-description>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="expiryDateLabel">Expiry Date</x-input-span>
                                <x-form-input type="month" name="expiryDate" id="expiryDate" />
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
