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
                        <x-form-span>Create Unit Description</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-12">
                                <x-input-span id="longNameLabel">Long Name</x-input-span>
                                <x-form-input type="text" class="longName" name="longName" id="longName" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="shortNameLabel">Short Name</x-input-span>
                                <x-form-input type="text" class="shortName" name="shortName" id="shortName" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="nameLabel">Bed Number</x-input-span>
                                <x-form-input type="number" class="bedNumber" name="bedNumber" id="bedNumber" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="descriptionLabel">Description</x-input-span>
                                <x-form-input name="description" class="description" id="description" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="billLabel">Bill</x-input-span>
                                <x-form-input name="bill" class="bill" id="bill" value="" />
                            </x-form-div>
                            @if ($isUpdate)
                                <x-form-div class="col-xl-12">
                                    <x-input-span>Flag</x-input-span>
                                    <select class="form-select form-select-md" name="flag" id="flag">
                                        <option value="">Select</option>
                                        <option value="1">Yes</option>
                                        <option value="0">No</option>   
                                    </select>
                                </x-form-div>
                                <x-form-div class="col-xl-12">
                                    <x-input-span>Flag Reason</x-input-span>
                                    <x-form-input name="flagReason" class="flagReason" id="flagReason"/>
                                </x-form-div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="{{ $isUpdate ? 'saveWard' : 'createWard' }}Btn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Save' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
