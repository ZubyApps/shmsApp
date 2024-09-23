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
                            <x-form-div class="col-xl-6 {{ $isDoctor ? '' : 'd-none' }}">
                                <x-input-span>Current Diagnosis</x-input-span>
                                <x-form-textarea name="currentDiagnosis" class="currentDiagnosis" id="currentDiagnosis" readonly></x-form-textarea>
                            </x-form-div>
                            <x-form-div class="col-xl-6 {{ $isDoctor ? '' : 'd-none' }}">
                                <x-input-span>Admission Status</x-input-span>
                                <x-form-input name="admissionStatus" id="admissionStatus" readonly />
                            </x-form-div>
                        </div>
                    </div>
                    <div class="mt-4 form-control" id="appointmentDetails">
                        <x-form-span>Appointment Information</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span id="reasonLabel">To See<x-required-span /></x-input-span>
                                <select type="text" name="doctor" id="doctor" class="form-select form-select-md">
                                    <option value="">Choose Doctor</option>
                                    @foreach ($doctors as $doctor)
                                        <option value="{{ $doctor->id }}" name="{{ $doctor->username }}">{{ $doctor->username }}</option>
                                    @endforeach
                                </select>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Date<x-required-span /></x-input-span>
                                <x-form-input type="datetime-local" name="date" id="date"/>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span>Remark</x-input-span>
                                <x-form-textarea name="remark" id="remark"></x-form-textarea>
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
                <button type="button" id="saveAppointmentBtn" class="btn btn-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
