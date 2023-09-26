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
                            <x-form-div class="col-xl-6">
                                <x-input-span>Date<x-required-span /></x-input-span>
                                <x-form-input type="date" name="date" value="" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Surgeon<x-required-span /></x-input-span>
                                <x-form-input name="surgeon" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Assitant Surgeon</x-input-span>
                                <x-form-input name="assistantSurgeon" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Type of Aneasthesia</x-input-span>
                                <x-form-input name="typeOfAneasthesia" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Aneasthetist</x-input-span>
                                <x-form-input name="aneasthetist" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Scrub Nurse</x-input-span>
                                <x-form-input name="scrubNurse" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Type of Operation</x-input-span>
                                <x-form-input name="typeOfOperation" />
                            </x-form-div>
                        </div>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span>Surgical Procedure</x-input-span>
                                <x-form-textarea name="surgicalProcedure"></x-form-textarea>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Surgeon's Notes</x-input-span>
                                <x-form-textarea name="surgeonsNotes"></x-form-textarea>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Aneasthetist's Notes</x-input-span>
                                <x-form-textarea name="assistantSurgeon"></x-form-textarea>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Post Operarion <br> Notes</x-input-span>
                                <x-form-textarea name="assistantSurgeon" ></x-form-textarea>
                            </x-form-div>
                        </div>
                    </div>
                </div>
                <div class="mb-2">
                    <x-form-label>Anesthesiologist's Notes</x-form-label>
                    <div class=" allSponsorInputsDiv">
                        <x-form-span>Patients data </x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span>Pre-assessment</x-input-span>
                                <x-form-input name="pre-assessment" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Indication<x-required-span /></x-input-span>
                                <x-form-input name="indication" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Surgery</x-input-span>
                                <x-form-input name="surgery" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Plan</x-input-span>
                                <x-form-input name="plan" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Pre-med</x-input-span>
                                <x-form-input name="pre-med" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Baseline</x-input-span>
                                <x-form-input name="baseline" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Cannulation</x-input-span>
                                <x-form-input name="cannulation" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Pre-med</x-input-span>
                                <x-form-input name="pre-med" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Preloading</x-input-span>
                                <x-form-input name="preloading" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Induction</x-input-span>
                                <x-form-input name="induction" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Maintenance</x-input-span>
                                <x-form-input name="maintenance" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Infusion</x-input-span>
                                <x-form-input name="infusion" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Analgesics</x-input-span>
                                <x-form-input name="analgesics" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Transfusion</x-input-span>
                                <x-form-input name="transfusion" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Antibiotics</x-input-span>
                                <x-form-input name="antibiotics" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Maintenance</x-input-span>
                                <x-form-input name="maintenance" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>KOS</x-input-span>
                                <x-form-input name="kos" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>EOS</x-input-span>
                                <x-form-input name="eos" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>EBL</x-input-span>
                                <x-form-input name="ebl" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Immediate post-op</x-input-span>
                                <x-form-input name="maintenance" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Tourniquet time</x-input-span>
                                <x-form-input name="tourniquetTime" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Tourniquet out</x-input-span>
                                <x-form-input name="tourniquetOut" />
                            </x-form-div>
                        </div>
                        <x-form-span>Baby Details </x-form-span>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span>Baby out</x-input-span>
                                <x-form-input name="babyOut" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Sex</x-input-span>
                                <x-form-input name="sex" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Apgar Score</x-input-span>
                                <x-form-input name="apgarScore" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Weight</x-input-span>
                                <x-form-input name="weight" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Surgeon</x-input-span>
                                <x-form-input name="surgeon" />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Anaesthetist</x-input-span>
                                <x-form-input name="anaesthetist" />
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
                <button type="button" id="{{ $isUpdate ? 'saveBtn' : 'createBtn' }}" class="btn bg-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button>
            </div>
        </div>
    </div>
</div>
