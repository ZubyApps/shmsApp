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
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Age</x-input-span>
                                    <x-form-input name="age" value="" id="age" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Sex</x-input-span>
                                    <x-form-input name="sex" value="" id="sex" readonly/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control" id="medicalReportDetailsDiv">
                            <div class="row">
                                <x-form-span class="fs-5">Enter Details</x-form-span>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Dr (Name in full) <x-required-span /></x-input-span>
                                    <x-form-input name="doctor" id="doctor"/>
                                </x-form-div>                        
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Designation <x-required-span /></x-input-span>
                                    <x-form-input name="designation" id="designation" placeholder="MBBS...etc"/>
                                </x-form-div>                        
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Report/Doc Type <x-required-span /></x-input-span>
                                    <x-form-input name="type" name="type" id="type" placeholder="Medical Report, Scan report...etc"/>
                                </x-form-div>                        
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Requested By</x-input-span>
                                    <select class="form-select form-select-md" name="requestedBy" id="requestedBy">
                                        <option value="Patient">Patient</option>
                                        <option value="Next of Kin">Next of Kin</option>
                                        <option value="Extended Family">Extended Family</option>
                                        <option value="Attending Physician">Attending Physician</option>
                                        <option value="Employer">Employer</option>
                                        <option value="Law Enforcement/Legal Bodies">Law Enforcement/Legal Bodies</option>
                                    </select>
                                </x-form-div>
                                <x-form-div class="col-xl-12">
                                    <x-input-span id="physiciansPlanLabel">Recipients Address (optional)
                                        <i class="btn bi bi-type-bold emboldenBtn"></i>
                                        <i class="btn bi bi-type-italic italicsBtn"></i>
                                        <i class="btn bi bi-type-underline underlineBtn"></i>
                                    </x-input-span>
                                    <div class="form-control" type="text" name="recipientsAddress" id="recipientsAddress" contentEditable></div>
                                </x-form-div>                        
                                <x-form-div class="col-xl-12">
                                    <x-input-span id="physiciansPlanLabel">Report<x-required-span />
                                        <i class="btn bi bi-type-bold emboldenBtn"></i>
                                        <i class="btn bi bi-type-italic italicsBtn"></i>
                                        <i class="btn bi bi-type-underline underlineBtn"></i>
                                    </x-input-span>
                                    <div class="form-control" type="text" name="report" id="report" contentEditable></div>
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
                    <button type="button" id="{{ $isUpdate ? 'saveMedicalReportBtn' : 'createMedicalReportBtn' }}" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $isUpdate ? 'Save' : 'Create' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
