<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <div class="patientInfoDiv form-control mb-2">
                        <x-form-span>Patient Details</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span id="patientLabel">Patient</x-input-span>
                                <x-form-input name="patientIds" readonly id="patient"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span id="sponsorNameLabel">Sponsor Name</x-input-span>
                                <x-form-input type="search" class="sponsorName" name="sponsorName" id="sponsorName" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Diagnosis</x-input-span>
                                <x-form-input name="diagnosis" class="" id="diagnosis" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Investigation</x-input-span>
                                <x-form-input type="text" name="investigation" id="investigation" readonly />
                            </x-form-div>
                        </div>
                    </div>
                    <div class="mt-4 form-control" id="removalReasonDiv">
                        <x-form-span>Reason For Removal</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-12">
                                <x-input-span id="reasonLabel">Reason<x-required-span /></x-input-span>
                                <x-select-test-not-done aria-label="removalReason" name="removalReason" id="removalReason"></x-select-test-not-done>
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
                <button type="button" id="saveRemovalReasonBtn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
