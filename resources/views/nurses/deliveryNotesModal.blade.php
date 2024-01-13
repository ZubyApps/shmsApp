<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Delivery Note</x-form-label>
                    <div class="row">
                        <x-form-div>
                            <x-input-span>Date<x-required-span /></x-input-span>
                            <x-form-input type="date" name="date" value="" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Date of Admission<x-required-span /></x-input-span>
                            <x-form-input type="datetime-local" name="timeOfAdmission" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Date of Delivery<x-required-span /></x-input-span>
                            <x-form-input type="datetime-local" name="timeOfDelivery" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Apgar Score<x-required-span /></x-input-span>
                            <x-form-input name="apgarScore" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Birth Weight<x-required-span /></x-input-span>
                            <x-form-input name="birthWeight" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Mode of Delivery<x-required-span /></x-input-span>
                            <x-form-input name="modeOfDelivery" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Lenght of Parity<x-required-span /></x-input-span>
                            <x-form-input name="lengthOfParity" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Head Circumference<x-required-span /></x-input-span>
                            <x-form-input name="headCircumference" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Sex<x-required-span /></x-input-span>
                            <x-form-input name="sex" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>EBL<x-required-span /></x-input-span>
                            <x-form-input name="ebl" />
                        </x-form-div>
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
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
