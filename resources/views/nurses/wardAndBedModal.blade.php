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
                                <x-form-input name="patient" id="patient" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span id="sponsorNameLabel">Sponsor Name</x-input-span>
                                <x-form-input class="sponsorName" name="sponsorName" id="sponsorName" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Current Diagnosis</x-input-span>
                                <x-form-textarea name="currentDiagnosis" class="currentDiagnosis" id="currentDiagnosis" readonly></x-form-textarea>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Admission Status</x-input-span>
                                <x-form-input name="admissionStatus" id="admissionStatus" readonly />
                            </x-form-div>
                        </div>
                    </div>
                    <div class="mt-4 form-control" id="wardAndBedDiv" data-div="updateModal">
                        <x-form-span>Update Patient's Admission Status</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-4">
                                <x-input-span id="admitLabel">Admit?<x-required-span /></x-input-span>
                                <x-select-admit name="admit" id="admit" :disabled="false"></x-select-admit>
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span id="wardLabel">Ward</x-input-span>
                                <x-select-ward name="ward" id="ward"></x-select-ward>
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span id="bedNumberLabel">Bed Number</x-input-span>
                                <x-select-bed name="bedNumber" id="bedNumber"></x-select-bed>
                            </x-form-div>
                        </div>
                        <div class="d-flex justify-content-between my-2">
                            <span class="input-group-text" id="updatedBy"></span>
                            <span class="input-group-text" id="doctor"></span>
                        </div>
                        <x-toast-successful  id="saveUpdateAdmissionStatusToast"></x-toast-successful>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="saveWardAndBedBtn" class="btn btn-primary text-white" data-btn="updateModal">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
