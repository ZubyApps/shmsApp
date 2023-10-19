<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Chart Medication</x-form-label>
                    <div class="row">
                        <x-form-div class="col-xl-6">
                            <x-input-span>Name</x-input-span>
                            <x-form-input name="name" value="" />
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span>Description</x-input-span>
                            <x-form-textarea name="description" value="" rows="1"></x-form-textarea>
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span>Consultation Fee</x-input-span>
                            <x-form-input type="number" name="consultationFee" value="" />
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span id="payClassLabel">Pay Class<x-required-span /></x-input-span>
                            <select class="form-select form-select-md" name="payClass">
                                <option value="">Select Class</option>
                                <option value="Cash">Cash</option>
                                <option value="Credit">Credit</option>
                            </select>
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span>Approval<x-required-span /></x-input-span>
                            <select class="form-select form-select-md" name="approval">
                                <option value="">Select Option</option>
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span id="billMatrixLabel">Bill Matrix %<x-required-span /></x-input-span>
                            <select class="form-select form-select-md" name="billMatrix">
                                <option value="">Select Value</option>
                                <option value="100">100%</option>
                                <option value="40">40%</option>
                                <option value="10">10%</option>
                                <option value="0">0%</option>
                            </select>
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span id="maritalStatusLabel">Balance Required?<x-required-span /></x-input-span>
                            <select class="form-select form-select-md" aria-label="balance-required"
                                name="balanceRequired">
                                <option value="">Select Option</option>
                                <option value="true">Yes</option>
                                <option value="false">No</option>
                            </select>
                        </x-form-div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="{{ $isUpdate ? 'createSponsorCategory' : 'createSponsorCategory' }}Btn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Save' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
