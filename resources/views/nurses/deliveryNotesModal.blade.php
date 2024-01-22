@vite(['resources/js/deliveryNoteMasks.js'])
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
                            <x-form-input type="date" name="date" :readonly="$isView"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Time of Admission<x-required-span /></x-input-span>
                            <x-form-input type="datetime-local" name="timeOfAdmission" :readonly="$isView"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Time of Delivery<x-required-span /></x-input-span>
                            <x-form-input type="datetime-local" name="timeOfDelivery" :readonly="$isView"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Apgar Score<x-required-span /></x-input-span>
                            <x-form-input type="number" name="apgarScore" :readonly="$isView"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Birth Weight<x-required-span /></x-input-span>
                            <x-form-input name="birthWeight" id="birthWeight" :readonly="$isView"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Mode of Delivery<x-required-span /></x-input-span>
                            <select class = "form-select form-select-md" name="modeOfDelivery" :readonly="$isView">
                                <option value="">Select</option>
                                <option value="Spontenous Delivery">Spontenous Delivery</option>
                                <option value="Cesarean section">Cesarean section</option>
                            </select>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Lenght of Parity<x-required-span /></x-input-span>
                            <x-form-input name="lengthOfParity" id="lengthOfParity" :readonly="$isView"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Head Circumference<x-required-span /></x-input-span>
                            <x-form-input name="headCircumference" id="headCircumference" :readonly="$isView"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Sex<x-required-span /></x-input-span>
                            <select class = "form-select form-select-md" name="sex" :readonly="$isView">
                                <option value="">Select</option>
                                <option value="Spontenous Delivery">Female</option>
                                <option value="Cesarean section">Male</option>
                            </select>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>EBL<x-required-span /></x-input-span>
                            <x-form-input name="ebl" id="ebl" :readonly="$isView"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Note<x-required-span /></x-input-span>
                            <x-form-textarea name="note" :readonly="$isView"></x-form-textarea>
                        </x-form-div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="{{ $isUpdate ? 'saveBtn' : 'createBtn' }}" class="btn btn-primary {{ $isView ? 'd-none' : '' }}">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
