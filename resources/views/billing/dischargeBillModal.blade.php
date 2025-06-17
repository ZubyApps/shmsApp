
<div class="modal fade modal-md" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <x-form-div class="col-xl-12">
                        <x-input-span>Days<x-required-span /></x-input-span>
                        <x-form-input type="number" name="quantity" id="days"/>
                    </x-form-div>
                    <x-form-div class="col-xl-12">
                        <x-input-span>Ward Type<x-required-span /></x-input-span>
                        <select class="form-select form-select-md" name="wardType" id="wardType">
                            <option value="">Select</option>
                            <option value="General">General</option>
                            <option value="Private">Private</option>
                            <option value="Children">Children</option>
                            <option value="Observation">Observation</option>
                            <option value="e-Private">e-Private</option>
                            <option value="SemiVIP">SemiVIP</option>
                            <option value="VIP">VIP</option>
                        </select>
                    </x-form-div>
                    <x-form-div class="col-xl-12">
                        <x-input-span>Mark</x-input-span>
                        <select class="form-select form-select-md" name="mark" id="mark">
                            {{-- <option value="">Select</option> --}}
                            @foreach ($markedFors as $markedFor )
                                <option value="{{ $markedFor->id }}" name="{{ $markedFor->name }}" {{str_contains($markedFor->name, 'discharg') ? '' : 'disabled'}}>{{ $markedFor->name }}</option>
                            @endforeach
                        </select>
                    </x-form-div>
                </div>
            </div>
            <div class="modal-footer px-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="addBillBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    Add Bill
                </button>
            </div>
        </div>
    </div>
</div>