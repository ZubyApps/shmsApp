<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Surgery Details</x-form-label>
                    <div class="allSponsorInputsDiv">
                        <div class="row">
                            <x-form-div>
                                <x-input-span>Date<x-required-span /></x-input-span>
                                <x-form-input type="date" name="date" value="{{ date() }}" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Surgeon<x-required-span /></x-input-span>
                                <x-form-input name="surgeon" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Assitant Surgeon</x-input-span>
                                <x-form-input name="assistantSurgeon" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Type of Aneasthesia</x-input-span>
                                <x-form-input name="typeOfAneasthesia" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Anesthetist</x-input-span>
                                <x-form-input name="anesthetist" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Scrub Nurse</x-input-span>
                                <x-form-input name="scrubNurse" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Type of Operation</x-input-span>
                                <x-form-input name="typeOfOperation" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Surgical Procedure</x-input-span>
                                <x-form-textarea name="surgicalProcedure"></x-form-textarea>
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Surgeon's Notes</x-input-span>
                                <x-form-textarea name="surgeonsNotes"></x-form-textarea>
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Aneasthetist's Notes</x-input-span>
                                <x-form-textarea name="assistantSurgeon"></x-form-textarea>
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Post <br> Operarion <br> Notes</x-input-span>
                                <x-form-textarea name="assistantSurgeon" ></x-form-textarea>
                            </x-form-div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <x-form-label>Anesthesiologist's Notes</x-form-label>
                    <div class="{{ !$isUpdate ? 'd-none' : '' }} allSponsorInputsDiv">
                        <div class="row">
                            <x-form-div>
                                <x-input-span>Name<x-required-span /></x-input-span>
                                <x-form-input name="sponsorName" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Phone No.<x-required-span /></x-input-span>
                                <x-form-input name="phoneNumber" />
                            </x-form-div>
                            <x-form-div>
                                <x-input-span>Email</x-input-span>
                                <x-form-input name="email" />
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
                <button type="button" id="{{ $isUpdate ? 'saveBtn' : 'createBtn' }}" class="btn bg-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
