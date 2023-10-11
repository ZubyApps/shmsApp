<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Document Details</x-form-label>
                    <div class="allSponsorInputsDiv">
                        <div class="row">
                            <x-form-div class="col-xl-12">
                                <x-input-span>Name File<x-required-span /></x-input-span>
                                <x-form-input name="name" placeholder="eg: x-ray, ultrasound scan" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-form-input type="file" name="file" />
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
                <button type="button" id="{{ $isUpdate ? 'saveBtn' : 'createBtn' }}" class="btn bg-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    Create
                </button>
            </div>
        </div>
    </div>
</div>
