<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    {{-- <x-form-label>Verify HMO Patient</x-form-label> --}}
                    <div class="patientInfoDiv form-control mb-2">
                        <x-form-span>Save a third party service for this patient</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-12">
                                <x-input-span id="descriptionLabel">Patient</x-input-span>
                                <x-form-input name="patient" id="patient" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="descriptionLabel">Service</x-input-span>
                                <x-form-input name="service" id="service" />
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span>Third Party (Org)<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" id="thirdParty" name="thirdParty">
                                    <option value="">Select</option>   
                                    @foreach ($thirdParties as $thirdParty )
                                        <option value="{{ $thirdParty->id}}" name="{{ $thirdParty->short_name }}">{{ $thirdParty->short_name }}</option>
                                    @endforeach
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
                <button type="button" id="saveThirPartyServiceBtn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Save 
                </button>
            </div>
        </div>
    </div>
</div>
