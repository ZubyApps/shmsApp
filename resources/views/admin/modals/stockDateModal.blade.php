<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    {{-- <x-form-label>Verify HMO Patient</x-form-label> --}}
                    <div class="patientInfoDiv form-control mb-2">
                        <x-form-span>Create new Resource stock date</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span id="descriptionLabel">Description</x-input-span>
                                <x-form-input name="description" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span id="dateLabel">Date</x-input-span>
                                <x-form-input type="datetime-local" class="date" name="date" value="" />
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
                <button type="button" id="{{ $isUpdate ? 'saveResourceStockDate' : 'createResourceStockDate' }}Btn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Save' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
