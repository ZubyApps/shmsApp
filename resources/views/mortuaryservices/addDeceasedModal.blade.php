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
                    <x-form-label>Deceased's Information</x-form-label>
                    <!-- first row -->
                    <div class="form-control">
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span>Name Of Deceased<x-required-span /></x-input-span>
                                <x-form-input name="deceasedName" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span id="sexLabel">Sex of Deceased<x-required-span /></x-input-span>
                                <select class="form-select form-select-md sex" aria-label="sex" name="sex" id="sex">
                                    <option value="">Select</option>
                                    <option value="Female">Female</option>
                                    <option value="Male">Male</option>
                                </select>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span id="dateOfDepositLabel">Date of Deposit</x-input-span>
                                <x-form-input type="datetime-local" name="dateOfDeposit" id="dateOfDeposit"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Depositor<x-required-span /></x-input-span>
                                <x-form-input name="depositor" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Depositor's Address<x-required-span /></x-input-span>
                                <x-form-input name="depositorAddress" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Depositor's Phone<x-required-span /></x-input-span>
                                <x-form-input name="depositorPhone" type="number"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Depositor's Relationship<x-required-span /></x-input-span>
                                <x-select-nok name="depositorRship" id="depositorRship"></x-select-nok>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Alternative Collector</x-input-span>
                                <x-form-input name="altCollectorName" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Alt Collector's Address<x-required-span /></x-input-span>
                                <x-form-input name="altCollectorAddress" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Alternative Collector Phone<x-required-span /></x-input-span>
                                <x-form-input name="altCollectorPhone" type="number"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Alternative Collector Relationship</x-input-span>
                                <x-select-nok name="altCollectorRship" id="altCollectorRship"></x-select-nok>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span id="DateOfPickUpLabel">PickUp Date </x-input-span>
                                <x-form-input type="date" name="pickUpDate" id="pickUpDate"/>
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
                <button type="button" id="{{ $isUpdate ? 'saveDeceasedRecordBtn' : 'createDeceasedRecordBtn' }}" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
