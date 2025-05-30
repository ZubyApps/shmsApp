<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="mb-2 form-control">
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Patient</x-input-span>
                                    <x-form-input name="patient" value="" id="patient" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Sponsor</x-input-span>
                                    <x-form-input name="sponsorName" value="" id="sponsorName" readonly/>
                                </x-form-div>
                            </div>
                            <div class="row">
                                <x-form-div class="col-xl-12">
                                    <x-input-span>Shift</x-input-span>
                                    <select class="form-select form-select-md" name="shift" id="shift">
                                        <option value="">Select</option>
                                        <option value="Morning Shift">Morning Shift</option>
                                        <option value="Afternoon Shift">Afternoon Shift</option>
                                        <option value="Night Shift">Night Shift</option>
                                    </select>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control" id="medicalReportDetailsDiv">
                            <div class="row">
                                <x-form-span class="fs-5">Enter Details</x-form-span>                        
                                <x-form-div class="col-xl-12">
                                    <x-input-span id="physiciansPlanLabel">Report<x-required-span /></x-input-span>
                                    <x-form-textarea class="form-control" type="text" name="report" id="report" rows="7" cols="10"></x-form-textarea>
                                </x-form-div>                        
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="button" id="{{ $isUpdate ? 'saveNursesReportBtn' : 'createNursesReportBtn' }}" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $isUpdate ? 'Save' : 'Create' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
