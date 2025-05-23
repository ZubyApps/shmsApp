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
                    <x-form-label>Initial Examination Information</x-form-label>
                    <div class="form-control">
                        <x-form-span class="mb-2">BOOKED/UNBOOKED</x-form-span>
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
                                        <x-form-input type="text" class="sponsor fw-semibold" id="sponsorName" readonly/>
                                    </x-form-div>
                                        
                                </div>
                            </div>
                            <div class="mb-2">
                                <x-form-span>First Examination</x-form-span>
                                <!-- first row -->
                                <div class="mb-2 form-control" id="registerationDiv" data-div="{{ $isUpdate ? 'updateLabour' : 'newLabour' }}">
                                    <x-form-span>History</x-form-span>
                                    <div class="row">
                                        <x-form-div class="col-xl-6">
                                            <x-input-span>Parity</x-input-span>
                                            <x-form-input type="text" name="parity" :readonly="$isView"/>
                                        </x-form-div>
        
                                        <x-form-div class="col-xl-6">
                                            <x-input-span >No. of Living Children</x-input-span>
                                            <x-form-input name="noOfLivingChildren" :readonly="$isView"/>
                                        </x-form-div>
        
                                        <x-form-div class="col-xl-6">
                                            <x-input-span >Past Obstetric History</x-input-span>
                                            <x-form-textarea name="pastObHistory" :readonly="$isView"></x-form-textarea>
                                        </x-form-div>
        
                                        <x-form-div class="col-xl-6">
                                            <x-input-span >Antenatal History</x-input-span>
                                            <x-form-textarea name="antenatalHistory" :readonly="$isView"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span>LMP<x-required-span /></x-input-span>
                                            <x-form-input type="date" name="lmp" id="lmp" data-lmp="{{ $isUpdate ? 'updateLabour' : 'newLabour' }}"  :readonly="$isView"/>
                                        </x-form-div>
        
                                        <x-form-div>
                                            <x-input-span>EDD</x-input-span>
                                            <x-form-input type="date" name="edd" id="edd" readonly/>
                                        </x-form-div>
        
                                        <x-form-div>
                                            <x-input-span>EGA</x-input-span>
                                            <x-form-input name="ega" id="ega" readonly/>
                                        </x-form-div>
                                    </div>
                                </div>
                                <div class="mb-2 form-control">
                                    <x-form-span>Labour</x-form-span>
                                    <div class="row">
                                        <x-form-div>
                                            <x-input-span >Onset</x-input-span>
                                            <x-form-input name="onset" type="datetime-local" :readonly="$isView"/>
                                            <x-input-span >Hours</x-input-span>
                                            <x-form-input name="onsetHours" :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span class="py-0">Spontaneous</x-input-span>
                                            <x-form-input name="spontaneous" class="form-check-input py-3 mt-0 spontaneousInduced" type="checkbox" :disabled="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span class="py-0">Induced</x-input-span>
                                            <x-form-input name="induced" class="form-check-input py-3 mt-0 spontaneousInduced" type="checkbox" :disabled="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span class="py-0" >Amniotomy</x-input-span>
                                            <x-form-input name="amniotomy" class="form-check-input py-3 mt-0" type="checkbox" :disabled="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span class="py-0">Oxytocies</x-input-span>
                                            <x-form-input name="oxytocies" class="form-check-input py-3 mt-0" type="checkbox" :disabled="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span >Cervical DIlation</x-input-span>
                                            <x-form-input name="cervicalDilation"  :readonly="$isView"/>
                                            <x-input-span >cm</x-input-span>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span >Membrane Ruptured at</x-input-span>
                                            <x-form-input name="mRupturedAt" type="datetime-local"  :readonly="$isView"/>
                                        </x-form-div>
                                        <x-form-div>
                                            <x-input-span >Contractions (painful) began</x-input-span>
                                            <x-form-input name="contractionsBegan" type="datetime-local" :readonly="$isView"/>
                                        </x-form-div>
                                    </div>
                                </div>
                            </div>
                            <!-- second row -->
                            <div class="mb-2 form-control">
                                <x-form-span>General Condition</x-form-span>
                                <div class="row">
                                    <x-form-div class="col-xl-3">
                                        <x-input-span class="py-0">Excellent</x-input-span>
                                        <x-form-input name="excellent" class="form-check-input py-3 mt-0 gCondition" type="checkbox" :disabled="$isView"/>
                                    </x-form-div>
                                    <x-form-div class="col-xl-3">
                                        <x-input-span class="py-0">Good</x-input-span>
                                        <x-form-input name="good" class="form-check-input py-3 mt-0 gCondition" type="checkbox" :disabled="$isView"/>
                                    </x-form-div>
                                    <x-form-div class="col-xl-3">
                                        <x-input-span class="py-0" >Fair</x-input-span>
                                        <x-form-input name="fair" class="form-check-input py-3 mt-0 gCondition" type="checkbox" :disabled="$isView"/>
                                    </x-form-div>
                                    <x-form-div class="col-xl-3">
                                        <x-input-span class="py-0">Poor</x-input-span>
                                        <x-form-input name="poor" class="form-check-input py-3 mt-0 gCondition" type="checkbox" :disabled="$isView"/>
                                    </x-form-div>
                                </div>
                            </div>
                            <!-- third row -->
                            <div class="mb-2 form-control">
                                <x-form-span>Abdomen</x-form-span>
                                <div class="row">
                                    <x-form-div>
                                        <x-input-span >Fundal Height</x-input-span>
                                        <x-form-input name="fundalHeight" id="height" :readonly="$isView"/>
                                    </x-form-div>
                                    <x-form-div class="col-xl-3">
                                        <x-input-span class="py-0">Multiple</x-input-span>
                                        <x-form-input name="multiple" class="form-check-input py-3 mt-0 multipleSingleton" type="checkbox" :disabled="$isView"/>
                                    </x-form-div>
                                    <x-form-div class="col-xl-3">
                                        <x-input-span class="py-0">Singleton</x-input-span>
                                        <x-form-input name="singleton" class="form-check-input py-3 mt-0 multipleSingleton" type="checkbox" :disabled="$isView"/>
                                    </x-form-div>
                                    <x-form-div>
                                        <x-input-span >Lie</x-input-span>
                                        <x-form-input name="lie" :readonly="$isView"/>
                                    </x-form-div>
                                    <x-form-div>
                                        <x-input-span >Presentation</x-input-span>
                                        <x-form-input name="presentation" :readonly="$isView"/>
                                    </x-form-div>
                                    <x-form-div>
                                        <x-input-span >Position</x-input-span>
                                        <x-form-input name="position" :readonly="$isView"/>
                                    </x-form-div>
                                    <x-form-div>
                                        <x-input-span >Descent</x-input-span>
                                        <x-form-input name="descent" :readonly="$isView"/>
                                    </x-form-div>
                                    <x-form-div>
                                        <x-input-span >Foetal Heart Rate</x-input-span>
                                        <x-form-input name="foetalHeartRate" id="pulseRate" :readonly="$isView"/>
                                    </x-form-div>
                                </div>
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <x-form-span>PV</x-form-span>
                            <div class="row">
                                <x-form-div>
                                    <x-input-span >Vulva</x-input-span>
                                    <x-form-input name="vulva" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Vagina</x-input-span>
                                    <x-form-input name="vagina" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Cervix</x-input-span>
                                    <x-form-input name="cervix" :readonly="$isView"/>
                                    <x-input-span >% effaced</x-input-span>
                                </x-form-div>
                                <x-form-div class="col-xl-3">
                                    <x-input-span class="py-0">Well/loosely applied to PP</x-input-span>
                                    <x-form-input name="appliedToPp" class="form-check-input py-3 mt-0" type="checkbox" :disabled="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >OS</x-input-span>
                                    <x-form-input name="os" :readonly="$isView"/>
                                    <x-input-span >cm dilated</x-input-span>
                                </x-form-div>
                                <x-form-div class="col-xl-3">
                                    <x-input-span class="py-0">Membranes</x-input-span>
                                    <x-input-span class="py-0">Ruptred</x-input-span>
                                    <x-form-input name="membranesRuptured" class="form-check-input py-3 mt-0 mRupturedIntact" type="checkbox" :disabled="$isView"/>
                                    <x-input-span class="py-0">Intact</x-input-span>
                                    <x-form-input name="membranesIntact" class="form-check-input py-3 mt-0 mRupturedIntact" type="checkbox" :disabled="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >PP at O</x-input-span>
                                    <x-form-input name="ppAtO" :readonly="$isView"/>
                                    <x-input-span >Station in</x-input-span>
                                    <x-form-input name="stationIn" :readonly="$isView"/>
                                    <x-input-span >Position</x-input-span>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Caput</x-input-span>
                                    <x-form-input name="caput" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Moulding</x-input-span>
                                    <x-form-input name="moulding" :readonly="$isView"/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <x-form-span>Pelvis</x-form-span>
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span >S.P</x-input-span>
                                    <x-form-input name="sp" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span >Sacral Curve</x-input-span>
                                    <x-form-input name="sacralCurve" :readonly="$isView"/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <x-form-span>Forecast</x-form-span>
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span >Forecast</x-input-span>
                                    <x-form-input name="forecast" :readonly="$isView"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span >Ischial Spine</x-input-span>
                                    <x-form-input name="ischialSpine" :readonly="$isView"/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="row">
                            <x-form-div class="col-xl-6">
                                <x-input-span >Examiner</x-input-span>
                                <x-form-input name="examiner" :readonly="$isView"/>
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span >Designation</x-input-span>
                                <x-form-input name="designation" :readonly="$isView"/>
                            </x-form-div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    @if (!$isView)
                        <button type="button" id="{{ $isView ? 'deleteLabourRecordBtn' : ($isUpdate ? 'saveLabourRecordBtn' : 'createLabourRecordBtn') }}" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>
                            {{$isUpdate ? 'Save' : 'Create'}}
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
