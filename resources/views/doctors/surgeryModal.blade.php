@vite(['resources/js/deliveryNoteMasks.js'])
<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="">
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
                                    <input class="form-control" name="typeOfOperation" id="typeOfOperation" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-4">
                                    <x-input-span>Type of Aneasthesia<x-required-span /></x-input-span>
                                    <input class="form-control" name="typeOfAneasthesia" id="typeOfAneasthesia" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Surgeon<x-required-span /></x-input-span>
                                    <input class="form-control" name="surgeon" id="surgeon" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Assistant Surgeon</x-input-span>
                                    <input class="form-control" name="assistantSurgeon" id="assistantSurgeon" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Aneasthetist</x-input-span>
                                    <input class="form-control" name="aneasthetist" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Scrub Nurse<x-required-span /></x-input-span>
                                    <input class="form-control" name="scrubNurse" id="scrubNurse" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                            </div>
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Surgical Procedure<x-required-span /></x-input-span>
                                    <textarea class="form-control" name="surgicalProcedure" id="surgicalProcedure" {{ $isView ? 'readonly' : '' }} autocomplete="on"></textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Surgeon's Notes<x-required-span /></x-input-span>
                                    <textarea class="form-control" name="surgeonsNotes" id="surgeonsNotes" {{ $isView ? 'readonly' : '' }} autocomplete="on"></textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Aneasthetist's Notes<x-required-span /></x-input-span>
                                    <textarea class="form-control" name="aneasthetistsNotes" id="aneasthetistsNotes" {{ $isView ? 'readonly' : '' }} autocomplete="on"></textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Post Operarion <br> Notes<x-required-span /></x-input-span>
                                    <textarea class="form-control" name="postOperationNotes" id="postOperationNotes" {{ $isView ? 'readonly' : '' }} autocomplete="on"></textarea>
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
                                    <input class="form-control" name="preAssessment" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Indication</x-input-span>
                                    <input class="form-control" name="indication" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Surgery</x-input-span>
                                    <input class="form-control" name="surgery" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Plan</x-input-span>
                                    <input class="form-control" name="plan" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Pre-med</x-input-span>
                                    <input class="form-control" name="preMed" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Baseline</x-input-span>
                                    <input class="form-control" name="baseline" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Cannulation</x-input-span>
                                    <input class="form-control" name="cannulation" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Preloading</x-input-span>
                                    <input class="form-control" name="preloading" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Induction</x-input-span>
                                    <input class="form-control" name="induction" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Maintenance</x-input-span>
                                    <input class="form-control" name="maintenance" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Infusion</x-input-span>
                                    <input class="form-control" name="infusion" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Analgesics</x-input-span>
                                    <input class="form-control" name="analgesics" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Transfusion</x-input-span>
                                    <input class="form-control" name="transfusion" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Antibiotics</x-input-span>
                                    <input class="form-control" name="antibiotics" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>KOS</x-input-span>
                                    <input class="form-control" type="datetime-local" name="kos" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>EOS</x-input-span>
                                    <input class="form-control" type="datetime-local" name="eos" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>EBL</x-input-span>
                                    <input class="form-control" name="ebl" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Immediate post-op<x-required-span /></x-input-span>
                                    <textarea class="form-control" name="immediatePostOp" id="immediatePostOp" {{ $isView ? 'readonly' : '' }} autocomplete="on"></textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Tourniquet time</x-input-span>
                                    <input class="form-control" type="datetime-local" name="tourniquetTime" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Tourniquet out</x-input-span>
                                    <input class="form-control" type="datetime-local" name="tourniquetOut" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                            </div>
                            <x-form-span>Baby Details </x-form-span>
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Baby out</x-input-span>
                                    <input class="form-control" type="datetime-local" name="babyOut" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Sex<x-required-span /></x-input-span>
                                    <x-input-span class="">Female</x-input-span>
                                    <input class="form-control" type="number" name="female" id="female" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                    <x-input-span class="">Male</x-input-span>
                                    <input class="form-control" type="number" name="male" id="male" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Apgar Score</x-input-span>
                                    <input class="form-control" name="apgarScore" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Birth Weight</x-input-span>
                                    <input class="form-control" name="birthWeight" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>CS Surgeon</x-input-span>
                                    <input class="form-control" name="csSurgeon" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>CS Anaesthetist</x-input-span>
                                    <input class="form-control" name="csAneasthetist" {{ $isView ? 'readonly' : '' }} autocomplete="on"/>
                                </x-form-div>
                            </div>
                        </div>
                    </div>
                    {{-- </div> --}}
                </form>
            </div>
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
