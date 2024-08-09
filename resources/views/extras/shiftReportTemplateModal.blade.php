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
                                    <x-input-span>Shift</x-input-span>
                                    <select class="form-select form-select-md" name="shift" id="shift" {{ $isView ? 'disabled' : '' }}>
                                        <option value="">Select</option>
                                        <option value="Morning Shift">Morning Shift</option>
                                        <option value="Afternoon Shift">Afternoon Shift</option>
                                        <option value="Night Shift">Night Shift</option>
                                        <option {{ $dept == 'nurses' ? 'disabled' : '' }} value="Whole Day">Whole Day</option>
                                    </select>
                                </x-form-div>
                                <x-form-div class="col-xl-6  d-none">
                                    <x-input-span>Department</x-input-span>
                                    <x-form-input name="department" value="{{ $dept }}" id="department"/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control" id="medicalReportDetailsDiv">
                            <div class="row">
                                <x-form-span class="fs-5">Enter Details</x-form-span>                        
                                <x-form-div class="col-xl-12">
                                    <x-input-span id="reportLabel">Report<x-required-span /></x-input-span>
                                    <textarea class="form-control" type="text" name="report" id="report" rows="10" cols="5" {{ $isView ? 'readonly' : '' }}></textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-12 {{ $title == 'New Report' ? 'd-none' : '' }}">
                                    <x-input-span>Written By</x-input-span>
                                    <x-form-input name="writtenBy" id="writtenBy" readonly/>
                                    <x-input-span>Written At</x-input-span>
                                    <x-form-input name="writtenAt" id="writtenAt" readonly/>
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
                    <button type="button" id="{{ $isUpdate ? 'saveShiftReportBtn' : 'createShiftReportBtn' }}" class="btn btn-primary {{ $isView ? 'd-none' : '' }}">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $isUpdate ? 'Save' : 'Create' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
