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
                        <x-form-span>Patient Details</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span id="patientLabel">Patient</x-input-span>
                                <x-form-input name="patientId" id="patientId" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span id="sponsorNameLabel">Sponsor Name</x-input-span>
                                <x-form-input type="search" class="sponsorName" name="sponsorName" id="sponsorName" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Staff ID/No.</x-input-span>
                                <x-form-input name="staffId" class="staffId" id="staffId"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Phone Number</x-input-span>
                                <x-form-input type="tel" name="phoneNumber" id="phoneNumber" readonly />
                            </x-form-div>
                        </div>
                    </div>
                    <div class="mt-4 form-control" id="codeTextDiv">
                        <x-form-span>Fill Information</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span id="statusLabel">Status<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" id="status"
                                   name="status">
                                    <option value="">Select Option</option>
                                    <option value="Verified">Verified</option>
                                    <option value="Pending">Pending</option>
                                    <option value="Exponged">Exponged</option>
                                </select>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Enter Code/Text</x-input-span>
                                <x-form-input name="codeText" id="codeText" />
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
                <button type="button" id="verifyBtn" class="btn bg-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    Verify
                </button>
            </div>
        </div>
    </div>
</div>
