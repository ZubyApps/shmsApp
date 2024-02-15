<div class="modal fade modal-lg" id="serviceDoneModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary">Service Done</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2 form-control">
                    <div class="row">
                        <x-form-div class="col-xl-6">
                            <x-input-span>Patient</x-input-span>
                            <x-form-input name="patient" value="" id="patient" readonly />
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span>Treatment</x-input-span>
                            <x-form-input name="treatmemt" value="" id="treatment" readonly />
                        </x-form-div>
                        <x-form-div class="col-xl-12">
                            <x-input-span>Instruction</x-input-span>
                            <x-form-input name="instruction" value="" id="instruction" readonly   />
                        </x-form-div>
                    </div>
                </div>
                <div class="mb-2 form-control" id="saveServiceDoneDiv">
                    <div class="row">
                        <x-form-div class="col-xl-6">
                            <x-input-span>Report</x-input-span>
                            <x-form-input name="note" id="note"/>
                        </x-form-div>
                        <x-form-div class="col-xl-6">
                            <x-input-span>Not Done?</x-input-span>
                            <x-select-not-given aria-label="unit" name="notDone" id="notDone"></x-select-not-given>
                        </x-form-div>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-5">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" class="btn btn-primary saveServiceDoneBtn">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
        </div>
    </div>
</div>