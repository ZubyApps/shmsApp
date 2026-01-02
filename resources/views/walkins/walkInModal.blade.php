{{-- @vite(['resources/js/modals/sponsorModal.js']) --}}

<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>WalkIn's Information</x-form-label>
                    <!-- first row -->
                    <div class="form-control">
                        <div class="row">
                            <x-form-div class="col-xl-4">
                                <x-input-span>Firstname<x-required-span /></x-input-span>
                                <x-form-input name="firstName" />
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span>Middlename</x-input-span>
                                <x-form-input name="middleName" />
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span>Lastname<x-required-span /></x-input-span>
                                <x-form-input name="lastName" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span id="dateOfBirthLabel">Date of Birth<x-required-span /></x-input-span>
                                <x-form-input type="date" name="dateOfBirth" id="dateOfBirth"/>
                            </x-form-div>
                            <x-form-div>
                                <x-input-span id="sexLabel">Sex<x-required-span /></x-input-span>
                                <select class="form-select form-select-md sex" aria-label="sex" name="sex" id="sex">
                                    <option value="">Select</option>
                                    <option value="Female">Female</option>
                                    <option value="Male">Male</option>
                                </select>
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span>Phone No.<x-required-span /></x-input-span>
                                <x-form-input name="phone" type="number"/>
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span>Address</x-input-span>
                                <x-form-input name="address" />
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span>Occupations</x-input-span>
                                <x-form-input name="occupation" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span id="prevXrayLabel">Previous Xray</x-input-span>
                                <select class="form-select form-select-md" aria-label="prevXray"
                                    name="prevXray" id="prevXray">
                                    <option value="">Select</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </x-form-div>
                            <x-form-div>
                                <x-input-span id="dateOfPrevXrayLabel">Previous Xray Date </x-input-span>
                                <x-form-input type="date" name="dateOfXray" id="dateOfXray"/>
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span>Clinical Diagnosis</x-input-span>
                                <x-form-input name="clinicalDiagnosis" />
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span>Clinical Features</x-input-span>
                                <x-form-input name="clinicalFeatures" />
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
                <button type="button" id="{{ $isUpdate ? 'saveWalkInBtn' : 'createWalkInBtn' }}" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
