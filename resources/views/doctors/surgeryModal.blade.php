@vite(['resources/js/deliveryNoteMasks.js'])
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
                            <x-form-div class="col-xl-4">
                                <x-input-span>Date<x-required-span /></x-input-span>
                                <x-form-input type="date" name="date" id="date" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span>Type of Operation<x-required-span /></x-input-span>
                                <x-form-input name="typeOfOperation" id="typeOfOperation" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-4">
                                <x-input-span>Type of Aneasthesia<x-required-span /></x-input-span>
                                <x-form-input name="typeOfAneasthesia" id="typeOfAneasthesia" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Surgeon<x-required-span /></x-input-span>
                                <x-form-input name="surgeon" id="surgeon" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Assistant Surgeon</x-input-span>
                                <x-form-input name="assistantSurgeon" id="assistantSurgeon" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Aneasthetist</x-input-span>
                                <x-form-input name="aneasthetist" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Scrub Nurse<x-required-span /></x-input-span>
                                <x-form-input name="scrubNurse" id="scrubNurse" :readonly="$isView"/>
                            </x-form-div>
                        </div>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span>Surgical Procedure<x-required-span /></x-input-span>
                                <x-form-textarea name="surgicalProcedure" id="surgicalProcedure" :readonly="$isView"></x-form-textarea>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Surgeon's Notes<x-required-span /></x-input-span>
                                <x-form-textarea name="surgeonsNotes" id="surgeonsNotes" :readonly="$isView"></x-form-textarea>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Aneasthetist's Notes<x-required-span /></x-input-span>
                                <x-form-textarea name="aneasthetistsNotes" id="aneasthetistsNotes" :readonly="$isView"></x-form-textarea>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Post Operarion <br> Notes<x-required-span /></x-input-span>
                                <x-form-textarea name="postOperationNotes" id="postOperationNotes" :readonly="$isView"></x-form-textarea>
                            </x-form-div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <x-form-label>Anesthesiologist's Notes</x-form-label>
                    <div class="">
                        <x-form-span>Patients data </x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span>Pre-assessment</x-input-span>
                                <x-form-input name="preAssessment" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Indication</x-input-span>
                                <x-form-input name="indication" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Surgery</x-input-span>
                                <x-form-input name="surgery" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Plan</x-input-span>
                                <x-form-input name="plan" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Pre-med</x-input-span>
                                <x-form-input name="preMed" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Baseline</x-input-span>
                                <x-form-input name="baseline" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Cannulation</x-input-span>
                                <x-form-input name="cannulation" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Preloading</x-input-span>
                                <x-form-input name="preloading" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Induction</x-input-span>
                                <x-form-input name="induction" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Maintenance</x-input-span>
                                <x-form-input name="maintenance" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Infusion</x-input-span>
                                <x-form-input name="infusion" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Analgesics</x-input-span>
                                <x-form-input name="analgesics" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Transfusion</x-input-span>
                                <x-form-input name="transfusion" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Antibiotics</x-input-span>
                                <x-form-input name="antibiotics" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>KOS</x-input-span>
                                <x-form-input name="kos" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>EOS</x-input-span>
                                <x-form-input name="eos" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>EBL</x-input-span>
                                <x-form-input name="ebl" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Immediate post-op<x-required-span /></x-input-span>
                                <x-form-input name="immediatePostOp" id="immediatePostOp" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Tourniquet time</x-input-span>
                                <x-form-input type="datetime-local" name="tourniquetTime" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Tourniquet out</x-input-span>
                                <x-form-input type="datetime-local" name="tourniquetOut" :readonly="$isView"/>
                            </x-form-div>
                        </div>
                        <x-form-span>Baby Details </x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span>Baby out</x-input-span>
                                <x-form-input type="datetime-local" name="babyOut" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Sex<x-required-span /></x-input-span>
                                <x-input-span class="">Female</x-input-span>
                                <x-form-input type="number" name="female" id="female" :readonly="$isView"/>
                                <x-input-span class="">Male</x-input-span>
                                <x-form-input type="number" name="male" id="male" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Apgar Score</x-input-span>
                                <x-form-input name="apgarScore" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Birth Weight</x-input-span>
                                <x-form-input name="birthWeight" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>CS Surgeon</x-input-span>
                                <x-form-input name="csSurgeon" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>CS Anaesthetist</x-input-span>
                                <x-form-input name="csAneasthetist" :readonly="$isView"/>
                            </x-form-div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- </div> --}}
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                <button type="button" id="{{ $isUpdate ? 'saveSurgeryNoteBtn' : 'createSurgeryNoteBtn' }}" class="btn btn-primary {{ $isView ? 'd-none' : '' }}">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
