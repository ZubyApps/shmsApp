<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Chart Medication</x-form-label>
                    <div class="row">
                        <x-form-div>
                            <x-input-span>Patient</x-input-span>
                            <x-form-input name="patientsIds" value="SH23/7865 Patrick Abiodun Aso" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Diagnosis</x-input-span>
                            <x-form-input name="diagnosis" value="Clevical fracture secondary to RTA" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Medication<x-required-span /></x-input-span>
                            <x-form-input type="text" name="medication" value="Pentazocine Inj" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Prescription</x-input-span>
                            <x-form-input name="prescription" value="300mg 8hrly 2/7" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Prescribed By<x-required-span /></x-input-span>
                            <x-form-input type="text" name="prescriptionBy" value="Dr Toby" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Sponsor Name</x-input-span>
                            <x-form-input type="text" name="sponsorName" value="Axe Mansard" />
                        </x-form-div>
                        <div class="row" id="approveDiv">
                            <x-form-span class="mb-2 fw-semibold">Save the time intervals according to prescription</x-form-span>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Bill</x-input-span>
                                <x-form-input name="bill" value="6000" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>HMO's Tariff</x-input-span>
                                <x-form-input type="number" name="hmoTariff" value="" />
                            </x-form-div>
                            {{-- <div class="d-flex justify-content-center mt-2">
                                <button type="button" id="saveMedicationChartBtn" data-btn="${iteration}"
                                    class="btn btn-primary">
                                    save
                                </button>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="approveBtn" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Approve
                </button>
            </div>
        </div>
    </div>
</div>
