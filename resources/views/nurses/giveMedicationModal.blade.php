
<div class="modal fade modal-lg" id="giveMedicationModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Give Medication</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 form-control">
                    <div class="row">
                        <x-form-div class="col-xl-6">
                            <x-input-span>Patient</x-input-span>
                            <x-form-input name="patient" value="" id="patient"/>
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span>Treatment</x-input-span>
                            <x-form-input name="treatmemt" value="" id="treatment"/>
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span>Prescription</x-input-span>
                            <x-form-input name="prescription" value="" id="prescription"/>
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span>Dose</x-input-span>
                            <x-form-input name="Dose" value="" id="dose"/>
                        </x-form-div>
                    </div>
                </div>
                <div class="mb-2 form-control" id="giveMedicationDiv">
                    <x-form-div class="col-xl-12">
                        <x-input-span>Dose Given</x-input-span>
                        <x-form-input type="number" name="doseGiven" id="doseGiven" />
                        <x-input-span>Unit</x-input-span>
                        <select class="form-select form-select-md" aria-label="unit" name="unit" id="unit">
                            <option value="">Select</option>
                            <option value="mg">mg</option>
                            <option value="g">g</option>
                            <option value="ml(s)">ml(s)</option>
                            <option value="mega">mega</option>
                            <option value="IU">IU</option>
                            <option value="kg">kg</option>
                            <option value="ltr">ltr</option>
                            <option value="tab">tab</option>
                            <option value="cap">cap</option>
                        </select>
                    </x-form-div>
                    <x-form-div class="col-xl-12">
                        <x-input-span>Notes</x-input-span>
                        <x-form-input name="note" id="note"/>
                    </x-form-div>
                </div>
            </div>
            <div class="modal-footer px-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" class="btn btn-primary saveGivenMedicationBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>