<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Sponsor Information</x-form-label>
                    <div class="row">
                        <x-form-div1 class="my-2">
                            <x-input-span>Category<x-required-span /></x-input-span>
                            <select class="form-select form-select-md sponsorCategory1" name="category">
                                <option value="">Select Category</option>
                                <option value="family">Family</option>
                                <option value="nhis">NHIS</option>
                                <option value="hmo">HMO</option>
                                <option value="Company/Org">Company/Organization</option>
                            </select>
                        </x-form-div1>
                    </div>

                    <!-- first row -->
                    <div class="{{ !$isUpdate ? 'd-none' : '' }} allSponsorInputsDiv">
                        <div class="row">
                            <x-form-div1>
                                <x-input-span>Name<x-required-span /></x-input-span>
                                <x-form-input name="sponsorName" />
                            </x-form-div1>
                            <x-form-div1>
                                <x-input-span>Phone No.<x-required-span /></x-input-span>
                                <x-form-input name="phoneNumber" />
                            </x-form-div1>
                            <x-form-div1>
                                <x-input-span>Email</x-input-span>
                                <x-form-input name="email" />
                            </x-form-div1>
                            <x-form-div1>
                                <x-input-span id="payClassLabel">Pay Class<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" name="payClass">
                                    <option value="">Select Class</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Credit">Credit</option>
                                </select>
                            </x-form-div1>
                            <x-form-div1 class="registrationBillDiv1 d-none">
                                <x-input-span>Registration Bill<x-required-span /></x-input-span>
                                <select class="form-select form-select-md familyRegistrationBill"
                                    name="registrationBill">
                                    <option class="familyRegistrationBillOption" value="3500">3500</option>
                                    <option value="1500">1500 - Upgrade</option>
                                    <option value="Paid">Paid</option>
                                </select>
                            </x-form-div1>
                            <x-form-div1>
                                <x-input-span id="maritalStatusLabel">Bill Matrix %<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" name="billMatrix">
                                    <option value="">Select Value</option>
                                    <option value="100">100%</option>
                                    <option value="40">40%</option>
                                    <option value="10">10%</option>
                                    <option value="0">0%</option>
                                </select>
                            </x-form-div1>
                            <x-form-div1>
                                <x-input-span id="maritalStatusLabel">Balance
                                    Required?<x-required-span /></x-input-span>
                                <select class="form-select form-select-md" aria-label="balance-required"
                                    name="balanceRequired">
                                    <option value="Yes">Yes</option>
                                    <option value="No">No</option>
                                </select>
                            </x-form-div1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="{{ $isUpdate ? 'saveBtn' : 'createBtn' }}" class="btn bg-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
