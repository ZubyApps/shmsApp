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
                    <div class="mt-4 form-control" id="resultDiv">
                        <x-form-span>Fill Result</x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-12">
                                <x-input-span>Sample</x-input-span>
                                <x-form-input name="sample"> </x-form-input>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-input-span id="statusLabel">Result/Report<x-required-span /></x-input-span>
                                <x-form-textarea name="result"></x-form-textarea>
                            </x-form-div>
                            <x-form-div class="col-xl-12">
                                <x-form-input type="file" name="file" />
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
                <button type="button" id="saveResultBtn" class="btn bg-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>
