<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-form-label>Anc Patient's Overall Information</x-form-label>
                    <div class="form-control">
                        <div class="mb-2">
                            <x-form-span>Hospital Links</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="patientLabel">Patient<x-required-span /></x-input-span>
                                    <x-form-input name="patient" class="patient" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span>Age</x-input-span>
                                    <x-form-input name="age" class="age" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="sponsorLabel">Sponsor<x-required-span /></x-input-span>
                                    <x-form-input type="text" name="sponsor" class="sponsor" readonly/>
                                </x-form-div>
                                    
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-form-span>More Bio</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="maritalStatusLabel">Marital Status<x-required-span /></x-input-span>
                                    <select class="form-select form-select-md" aria-label="marital-status"
                                        name="maritalStatus" id="maritalStatus" {{ $isView ? 'disabled' : '' }}>
                                        <option value="">Select</option>
                                        <option value="Single">Single</option>
                                        <option value="Married">Married</option>
                                        <option value="Widow">Widow</option>
                                        <option value="Divorced">Divorced</option>
                                    </select>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="husbandNameLabel">Husband's Name<x-required-span /></x-input-span>
                                    <x-form-input name="husbandName" :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span id="husbandOccupationLabel">Husband's Occupation</x-input-span>
                                    <x-form-input name="husbandOccupation" :readonly="$isView"/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-form-span>History</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span>Heart Disease</x-input-span>
                                    <x-form-input type="text" name="heartDisease" :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span >Chest Disease</x-input-span>
                                    <x-form-input name="chestDisease" :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span >kidney Disease</x-input-span>
                                    <x-form-input name="kidneyDisease" :readonly="$isView"/>
                                </x-form-div>
                            </div>
                            <!-- second row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span >Blood Tansfusion</x-input-span>
                                    <x-form-input name="bloodTransfusion" :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span >Multiple Pregnancy</x-input-span>
                                    <x-form-input name="multiplePregnacy"  :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span >Diabetes</x-input-span>
                                    <x-form-input name="diabetes" :readonly="$isView"/>
                                </x-form-div>
                            </div>
                            <!-- third row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span >Hypertension</x-input-span>
                                    <x-form-input name="hypertension" :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span >sickle Cell</x-input-span>
                                    <x-form-input name="sickleCell"  :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span >Others</x-input-span>
                                    <x-form-input name="others" :readonly="$isView"/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2" id="registerationDiv" data-div="{{ $isUpdate ? 'updateAnc' : 'newAnc' }}">
                            <x-form-span>Related to Pregnancy</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div>
                                    <x-input-span>LMP<x-required-span /></x-input-span>
                                    <x-form-input type="date" name="lmp" id="lmp" data-lmp="{{ $isUpdate ? 'updateAnc' : 'newAnc' }}"  :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span>EDD</x-input-span>
                                    <x-form-input type="date" name="edd" id="edd" readonly/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span>EGA</x-input-span>
                                    <x-form-input name="ega" id="ega" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Previous Pregnancies<x-required-span /></x-input-span>
                                    <x-form-input name="previousPregnancies" id="previousPregnancies" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Total Pregnancies<x-required-span /></x-input-span>
                                    <x-form-input name="totalPregnancies" id="totalPregnancies" :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span >No. of Living Chidlren<x-required-span /></x-input-span>
                                    <x-form-input name="noOfLivingChildren" id="noOfLivingChildren" :readonly="$isView"/>
                                </x-form-div>

                                <x-form-div>
                                    <x-input-span >Bleeding</x-input-span>
                                    <x-form-input name="bleeding" id="bleeding" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Discharge</x-input-span>
                                    <x-form-input name="discharge" id="discharge" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Urinary Symptoms</x-input-span>
                                    <x-form-input name="urinarySymptoms" id="urinarySymptoms" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Swelling Of Ankles</x-input-span>
                                    <x-form-input name="swellingOfAnkles" id="swellingOfAnkles" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Other Symptoms</x-input-span>
                                    <x-form-input name="otherSymptoms" id="otherSymptoms" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >General Condition(Anemia)</x-input-span>
                                    <x-form-input name="generalConditionAnemia" id="generalConditionAnemia" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Oedema</x-input-span>
                                    <x-form-input name="oedema" id="oedema" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Anemia</x-input-span>
                                    <x-form-input name="anemia" id="anemia" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Abdomen</x-input-span>
                                    <x-form-input name="abdomen" id="abdomen" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Specimen</x-input-span>
                                    <x-form-input name="specimen" id="specimen" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Specimen CM</x-input-span>
                                    <x-form-input name="specimenCm" id="specimenCm" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Liver</x-input-span>
                                    <x-form-input name="liver" id="liver" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Liver CM</x-input-span>
                                    <x-form-input name="liverCm" id="liverCm" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Virginal Examination</x-input-span>
                                    <x-form-input name="virginalExamination" id="virginalExamination" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Other Anomalies</x-input-span>
                                    <x-form-input name="otherAnomalies" id="otherAnomalies" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Height</x-input-span>
                                    <x-form-input name="height" id="height" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Weight</x-input-span>
                                    <x-form-input name="weight" id="weight" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >BP</x-input-span>
                                    <x-form-input name="bp" id="bp" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Urine</x-input-span>
                                    <x-form-input name="urine" id="urine" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Breast/Nipples</x-input-span>
                                    <x-form-input name="breastNipples" id="breastNipples" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Hb</x-input-span>
                                    <x-form-input name="hb" id="hb" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Genotype</x-input-span>
                                    <x-form-input name="genotype" id="genotype" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >VDRL</x-input-span>
                                    <x-form-input name="vdrl" id="vdrl" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >GroupHR</x-input-span>
                                    <x-form-input name="groupHr" id="groupHr" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >RVST</x-input-span>
                                    <x-form-input name="rvst" id="rvst" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Comments</x-input-span>
                                    <x-form-input name="comments" id="comments" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Intructions relating to Pueperium</x-input-span>
                                    <x-form-input name="instrRelatingToPueperium" id="instrRelatingToPueperium" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Assessment</x-input-span>
                                    <x-form-input name="assessment" id="assessment" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >HB Genotype</x-input-span>
                                    <x-form-input name="hbGenotype" id="hbGenotype" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Chest Xray</x-input-span>
                                    <x-form-input name="chestXray" id="chestXray" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Rhesus</x-input-span>
                                    <x-form-input name="rhesus" id="rhesus" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Anti-Malaria & Specific Therapies</x-input-span>
                                    <x-form-input name="antiMalAndSpecificTherapies" id="antiMalAndSpecificTherapies" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Pelvic Assessment</x-input-span>
                                    <x-form-input name="pelvicAssessment" id="pelvicAssessment" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Instructions for Delivery</x-input-span>
                                    <x-form-input name="instructionsForDelivery" id="instructionsForDelivery" :readonly="$isView"/>
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
                    <button type="button" id="{{ $isView ? 'deleteAncBtn' : ($isUpdate ? 'saveAncBtn' : 'registerAncBtn') }}" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        {{$isView ? 'Delete' : ($isUpdate ? 'Save' : 'Register') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
