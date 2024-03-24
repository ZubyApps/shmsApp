{{-- @vite(['resources/js/modals/sponsorModal.js']) --}}

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Third Party Information</x-form-label>
                    <!-- first row -->
                    <div class="form-control">
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span>Full Name<x-required-span /></x-input-span>
                                <x-form-input name="fullName" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Short Name<x-required-span /></x-input-span>
                                <x-form-input name="shortName" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Phone No.</x-input-span>
                                <x-form-input name="phone" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Address</x-input-span>
                                <x-form-input name="address" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Email</x-input-span>
                                <x-form-input name="email" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Comment</x-input-span>
                                <x-form-input name="comment" />
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
                <button type="button" id="{{ $isUpdate ? 'saveThirdPartyBtn' : 'createThirdPartyBtn' }}" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
