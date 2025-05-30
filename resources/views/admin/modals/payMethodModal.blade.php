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
                        {{-- <x-form-span>Create new Resource stock date</x-form-span> --}}
                        <div class="row">
                            <x-form-div class="col-xl-12">
                                <x-input-span id="nameLabel">Name</x-input-span>
                                <x-form-input type="text" class="name" name="name" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="descriptionLabel">Description</x-input-span>
                                <x-form-input name="description" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Visible</x-input-span>
                                <select class="form-select form-select-md" name="visible">
                                    <option value="1">true</option>
                                    <option value="0">false</option>  
                                </select>
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
                <button type="button" id="{{ $isUpdate ? 'savePayMethod' : 'createPayMethod' }}Btn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Save' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
