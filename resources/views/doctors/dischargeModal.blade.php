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
                                <x-form-input class="sponsorName" name="sponsorName" id="sponsorName" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Current Diagnosis</x-input-span>
                                <x-form-input name="currentDiagnosis" class="currentDiagnosis" id="currentDiagnosis"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Admission Status</x-input-span>
                                <x-form-input name="admissionStatus" id="admissionStatus" readonly />
                            </x-form-div>
                        </div>
                    </div>
                    <div class="mt-4 form-control" id="dischargeDetails">
                        <x-form-span>Dicharge Information</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span id="reasonLabel">Reason<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" id="dischargeReason"
                                   name="dischargeReason" {{ $isNurses ? 'disabled' : '' }}>
                                    <option value="">Select Option</option>
                                    <option value="Treated">Treated</option>
                                    <option value="AHOR">AHOR</option>
                                    <option value="Referred">Referred</option>
                                    <option value="DAMA">DAMA</option>
                                    <option value="LTFU">LTFU</option>
                                    <option value="Diceased">Diceased</option>
                                </select>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Remark</x-input-span>
                                <x-form-textarea name="remark" id="remark" :readonly="$isNurses"></x-form-textarea>
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
                <button type="button" id="saveDischargeBtn" class="btn btn-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
