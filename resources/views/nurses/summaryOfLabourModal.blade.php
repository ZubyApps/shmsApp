@vite(['resources/js/vitalSignsMasks.js'])
<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <x-form-label>Summary of Labour Information</x-form-label>
                    <div class="form-control">
                        {{-- <x-form-span class="mb-2">BOOKED/UNBOOKED</x-form-span> --}}
                        <div class="mb-2 form-control">
                            <div class="mb-2 form-control">
                                <x-form-span >Patients Bio</x-form-span>
                                <!-- first row -->
                                <div class="row">
                                    <x-form-div>
                                        <x-input-span id="patientLabel">Patient<x-required-span /></x-input-span>
                                        <x-form-input class="patient fw-semibold" id="patient" readonly/>
                                    </x-form-div>
                                    <x-form-div>
                                        <x-input-span>Age</x-input-span>
                                        <x-form-input class="age fw-semibold" id="age" readonly/>
                                    </x-form-div>
                                    <x-form-div>
                                        <x-input-span id="sponsorLabel">Sponsor<x-required-span /></x-input-span>
                                        <x-form-input type="text" class="sponsorName fw-semibold" id="sponsorName" readonly/>
                                    </x-form-div>
                                        
                                </div>
                            </div>
                            <div class="mb-2">
                                <!-- first row -->
                                <div class="mb-2 form-control">
                                    <x-form-span>Induction of Labour</x-form-span>
                                    <div class="row">
                                        <x-form-div class="col-xl-6">
                                            <x-input-span class="py-0">Amniotomy</x-input-span>
                                            <x-form-input name="solAmniotomy" class="form-check-input py-3 mt-0" type="checkbox" :disabled="$isView"/>
                                            <x-input-span class="py-0">indication</x-input-span>
                                            <x-form-input name="solAIndication" class="py-0" :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span class="py-0">Oxytocin</x-input-span>
                                            <x-form-input name="solOxytocin" class="form-check-input py-3 mt-0" type="checkbox" :disabled="$isView"/>
                                            <x-input-span class="py-0">indication</x-input-span>
                                            <x-form-input name="solOIndication" class="py-0" :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span class="py-0">Prostaglandins</x-input-span>
                                            <x-form-input name="solProstaglandins" class="form-check-input py-3 mt-0" type="checkbox" :disabled="$isView"/>
                                            <x-input-span class="py-0">indication</x-input-span>
                                            <x-form-input name="solPIndication" class="py-0" :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span  class="py-0">Duration of Labour/Induction delivery interval</x-input-span>
                                            <x-form-input name="dOfLabour" class="py-1" :readonly="$isView"/>
                                            <x-input-span class="py-0">Hours</x-input-span>
                                        </x-form-div>
                                    </div>
                                </div>
                                <div class="mb-2 form-control">
                                    <x-form-span>Method of Delivery</x-form-span>
                                    <div class="row">
                                        <div class="row mb-2">
                                            <x-form-span><small>Cephalic Presentation</small> </x-form-span>
                                            <x-form-div>
                                                <x-input-span class="py-0">Spontaneous</x-input-span>
                                                <x-form-input name="solSpontaneous" class="form-check-input py-3 mt-0 methodOfDeliver" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">Assisted</x-input-span>
                                                <x-form-input name="solAssisted" class="form-check-input py-3 mt-0 methodOfDeliver" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0" >Forceps</x-input-span>
                                                <x-form-input name="solForceps" class="form-check-input py-3 mt-0 methodOfDeliver" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                        </div>         
                                        <div class="row mb-2">
                                            <x-form-span><small>Breech Presentation</small></x-form-span>
                                            <x-form-div>
                                                <x-input-span class="py-0">Extraction</x-input-span>
                                                <x-form-input name="extraction" class="form-check-input py-3 mt-0 methodOfDeliver" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">Vacuum</x-input-span>
                                                <x-form-input name="vacuum" class="form-check-input py-3 mt-0 methodOfDeliver" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">Internal Podalic Version</x-input-span>
                                                <x-form-input name="internalPodVersion" class="form-check-input py-3 mt-0 methodOfDeliver" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                        </div>
                                        <x-form-div>
                                            <x-input-span class="py-0">Caesarean Section(Emergency/Elective)</x-input-span>
                                            <x-form-input name="caesareanSection" class="form-check-input py-3 mt-0 methodOfDeliver" type="checkbox" :disabled="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span class="py-0">Destructive Operation(specify)</x-input-span>
                                            <x-form-input name="destructiveOperation" class="form-check-input py-4 mt-0 methodOfDeliver" type="checkbox" :disabled="$isView"/>
                                            <x-form-textarea name="dOSpecify" class="py-0" :readonly="$isView"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span class="py-0">Anaesthesia</x-input-span>
                                            <x-form-input name="anaesthesia" class="form-check-input py-3 mt-0" type="checkbox" :disabled="$isView"/>
                                        </x-form-div>
                                        <div class="row mb-2">
                                            <x-form-span><small>Placenta & Membranes</small></x-form-span>
                                            <x-form-div>
                                                <x-input-span class="py-0">Spontaneous</x-input-span>
                                                <x-form-input name="pSpontaneous" class="form-check-input py-3 mt-0 placentaMembranes" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">C.C.T</x-input-span>
                                                <x-form-input name="cct" class="form-check-input py-3 mt-0 placentaMembranes" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">Manual Removal</x-input-span>
                                                <x-form-input name="manualRemoval" class="form-check-input py-3 mt-0 placentaMembranes" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">Complete</x-input-span>
                                                <x-form-input name="complete" class="form-check-input py-3 mt-0 placentaMembranesState" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">Incomplete</x-input-span>
                                                <x-form-input name="incomplete" class="form-check-input py-3 mt-0 placentaMembranesState" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span  class="py-0">Weight</x-input-span>
                                                <x-form-input name="placentaWeight" class="py-1 weight" id="weight" :readonly="$isView"/>
                                            </x-form-div>
                                        </div>
                                        <div class="row mb-2">
                                            <x-form-span><small>Perineum</small></x-form-span>
                                            <x-form-div>
                                                <x-input-span class="py-0">Intact</x-input-span>
                                                <x-form-input name="perineumIntact" class="form-check-input py-3 mt-0 perineum" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">1st degree laceration</x-input-span>
                                                <x-form-input name="firstDegreeLaceration" class="form-check-input py-3 mt-0 perineum" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">2nd degree laceration</x-input-span>
                                                <x-form-input name="secondDegreeLaceration" class="form-check-input py-3 mt-0 perineum" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">3rd degree laceration</x-input-span>
                                                <x-form-input name="thirdDegreeLaceration" class="form-check-input py-3 mt-0 perineum" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span class="py-0">Episiotomy</x-input-span>
                                                <x-form-input name="episiotomy" class="form-check-input py-3 mt-0 perineum" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span  class="py-0">Repaired by</x-input-span>
                                                <x-form-input name="repairBy" class="py-1" :readonly="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span  class="py-0">Designation</x-input-span>
                                                <x-form-input name="designationRepair" class="py-1" :readonly="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span  class="py-0">No. of Skin Sutures</x-input-span>
                                                <x-form-input name="noOfSkinSutures" class="py-1" :readonly="$isView"/>
                                            </x-form-div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-2 form-control">
                                    <div class="row">
                                        <x-form-div>
                                            <x-input-span >Blood loss</x-input-span>
                                            <x-form-input name="bloodLoss" id="bloodLoss"  :readonly="$isView"/>
                                            {{-- <x-input-span >mls</x-input-span> --}}
                                        </x-form-div>
                                        <x-form-div class="col-xl-8">
                                            <x-input-span >Treatment</x-input-span>
                                            <x-form-textarea name="bloodLossTreatment" :readonly="$isView"></x-form-textarea>
                                        </x-form-div>
                                    </div>
                                </div>
                                <div class="mb-2 form-control">
                                    <x-form-span><small>Baby</small></x-form-span>
                                    <div class="mb-2 form-control">
                                        <div class="row">
                                            <x-form-div>
                                                <x-input-span class="py-0">Alive</x-input-span>
                                                <x-form-input name="alive" class="form-check-input py-3 mt-0 baby" type="checkbox" :disabled="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span  class="py-0">Sex(es)</x-input-span>
                                                <x-form-input name="sexes" class="py-1" :readonly="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span  class="py-0">Weight</x-input-span>
                                                <x-form-input name="babyWeight" id="weight" class="py-1" :readonly="$isView"/>
                                            </x-form-div>
                                            <x-form-div>
                                                <x-input-span  class="py-0">Apgar Score</x-input-span>
                                                <x-input-span  class="py-0">1 min</x-input-span>
                                                <x-form-input name="apgarScore1m" class="py-1" :readonly="$isView"/>
                                                <x-input-span  class="py-0">5 min</x-input-span>
                                                <x-form-input name="apgarScore5m" class="py-1" :readonly="$isView"/>
                                            </x-form-div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <x-form-div>
                                            <x-input-span class="py-0">Fresh Still Birth</x-input-span>
                                            <x-form-input name="freshStillBirth" class="form-check-input py-3 mt-0 baby" type="checkbox" :disabled="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span class="py-0">Macerated Still Birth</x-input-span>
                                            <x-form-input name="maceratedStillBirth" class="form-check-input py-3 mt-0 baby" type="checkbox" :disabled="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span class="py-0">Immediate Neonatal Death</x-input-span>
                                            <x-form-input name="immediateNeonatalDeath" class="form-check-input py-3 mt-0 baby" type="checkbox" :disabled="$isView"/>
                                        </x-form-div>
                                        <x-form-div class="col-xl-12">
                                            <x-input-span class="py-0">Malformation(s)</x-input-span>
                                            <x-form-input name="malformation" class="form-check-input py-4 mt-0" type="checkbox" :disabled="$isView"/>
                                            <x-input-span class="py-0">Details</x-input-span>
                                            <x-form-textarea name="malformationDetails" :readonly="$isView" class="py-0"></x-form-textarea>
                                        </x-form-div>
                                    </div>
                                </div>
                                <div class="mb-2 form-control">
                                    <div class="row">
                                        <x-form-span><small>Mother's Condition 1 hour Post-Partum</small></x-form-span>
                                        <x-form-div>
                                            <x-input-span  class="py-0">Uterus</x-input-span>
                                            <x-form-input name="mcUterus" class="py-1" :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span  class="py-0">Bladder</x-input-span>
                                            <x-form-input name="mcBladder" class="py-1" :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span  class="py-0">Blood Pressure</x-input-span>
                                            <x-form-input name="mcBloodPressure" id="bloodPressure" class="py-1" :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span  class="py-0">Pulse Rate</x-input-span>
                                            <x-form-input name="mcPulseRate" id="pulseRate" class="py-1" :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span  class="py-0">Temperature</x-input-span>
                                            <x-form-input name="mcTemperature" id="temperature" class="py-1" :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span  class="py-0">Respiration</x-input-span>
                                            <x-form-input name="mcRespiration" id="respiratoryRate" class="py-1" :readonly="$isView"/>
                                        </x-form-div>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <x-form-div>
                                        <x-input-span  class="py-0">Supervisor</x-input-span>
                                        <x-form-input name="supervisor" class="py-1" :readonly="$isView"/>
                                    </x-form-div>
                                    <x-form-div class="col-xl-8">
                                        <x-input-span class="py-0">Accoucheur</x-input-span>
                                        <x-form-textarea name="accoucheur" :readonly="$isView" class="py-0"></x-form-textarea>
                                    </x-form-div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    @if (!$isView)
                    <button type="button" id="{{ $isUpdate ? 'saveLabourSummaryBtn' : 'deleteLabourSummaryBtn' }}" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>
                        {{$isUpdate ? 'Save' : 'Delete'}}
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
