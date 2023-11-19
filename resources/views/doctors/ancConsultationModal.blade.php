<div class="container">
    <div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="mb-2 form-control">
                            @include('patients.partials.patientBio')
                        </div>
                        <div class="mb-2 form-control">
                            <X-form-span class="fw-semibold">Previously Known Clinical Info</X-form-span>
                            <div class="row" id="knownClinicalInfoDiv" data-div="anc">
                                <x-toast-successful class="col-xl-12"  id="knownClinicalInfoToast"></x-toast-successful>
                                @include('patients.partials.known-clinical-info', ['disabled' => true])
                                <div class="d-flex justify-content-center">
                                    <button type="button" id="updateKnownClinicalInfoBtn" class="btn bg-primary text-white" data-btn="anc">
                                        <i class="bi bi-arrow-up-circle"></i>
                                        Update
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card card-body">
                            <div class="mb-2 form-control vitalsDiv">
                                <x-form-span>Vital Signs</x-form-span>
                                <div class="row overflow-auto my-3">
                                    <table id="vitalSignsTable{{ $isReview ? 'AncReview' : 'Anc' }}" class="table table-hover align-middle table-sm vitalsTable">
                                        <thead>
                                            <tr>
                                                <th>Done</th>
                                                <th>Temp</th>
                                                <th>BP</th>
                                                <th>Resp Rate</th>
                                                <th>SpO2</th>
                                                <th>Pulse</th>
                                                <th>Weight</th>
                                                <th>Height</th>
                                                <th>By</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="row" id="addVitalsignsDiv" {!! $isReview ? 'data-div="ancReview"' : 'data-div="anc"' !!}>
                                        <x-toast-successful class="col-xl-12"  id="vitalSignsToast"></x-toast-successful>
                                        @include('vitalsigns.vitalsigns', ['disabled' => true])
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="addVitalsignsBtn" {!! $isReview ? 'data-btn="ancReview"' : 'data-btn="anc"' !!}
                                            class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            add
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="consultationParentDiv">
                                <div class="mb-2 form-control " id="consultationDiv" {!! $isReview ? 'data-div="ancReview"' : 'data-div="anc"' !!}>
                                    <x-form-label>Consultation {{ $isReview ? "Review" : '' }}</x-form-label>
                                    <div class="row">
                                        <x-form-div class="col-xl-12">
                                            <x-input-span id="specialistDesignationLabel">Consultant (Name&Designation) </x-input-span>
                                            <x-form-input name="consultantSpecialist" value=""
                                                placeholder="if applicable..." />
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span>LMP</x-input-span>
                                            <x-form-input type="date" name="lmp"/>
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span>EDD</x-input-span>
                                            <x-form-input type="date" name="edd"/>
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span>EGA</x-input-span>
                                            <x-form-input name="ega"/>
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span>Fetal Heart Rate</x-input-span>
                                            <x-form-input type="text" name="fetalHeartRate" />
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span>Height of Fundus</x-input-span>
                                            <x-form-input type="text" name="heightOfFundus" />
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span id="presentationPositionLabel">Presentation&Position</x-input-span>
                                            <x-form-input name="presentationAndPosition" id="presentationAndPosition" />
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="examinationFindingsLabel">Relation of <br>
                                                Presenting Part to Brim</x-input-span>
                                            <x-form-textarea type="text" name="relationOfPresentingPartToBrim"
                                                id="relationOfPresentingPartToBrim" cols="10"
                                                rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="obGyneHistoryLabel">Obstetrics/<br />Gynecological History</x-input-span>
                                            <x-form-textarea type="text" name="obGynHistory" id="obGynHistory"
                                                cols="10" rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="ultrasoundReportLabel">Ultrasound Report</x-input-span>
                                            <x-form-textarea name="ultrasoundReport" id="ultrasoundReport" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="remarksLabel">Remarks </x-input-span>
                                            <x-form-textarea type="text" name="remarks" class="remarks" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-12">
                                            <x-input-span>Search <br />for ICD11 Diagnosis</x-input-span>
                                            @if ($isReview) <x-icd11-diagnosis-input :number="5" /> @else <x-icd11-diagnosis-input :number="4" /> @endif
                                        </x-form-div>
                                        @if ($isReview) <x-icd11-diagnosis-div :number="5" /> @else <x-icd11-diagnosis-div :number="4" /> @endif
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="selectedDiagnosisLabel">Selected <br />ICD11 Diagnosis</x-input-span>
                                            <x-form-textarea type="text" name="selectedDiagnosis"
                                                class="selectedDiagnosis-{{ $isReview ? '5' : '4' }}"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="physiciansPlanLabel">Physicians Notes</x-input-span>
                                            <x-form-textarea type="text" name="notes" id="notes"
                                                cols="10" rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="physiciansPlanLabel">Physicians Plan</x-input-span>
                                            <x-form-textarea type="text" name="plan" id="plan"
                                                cols="10" rows="3"></x-form-textarea>
                                        </x-form-div>
                                    </div>
                                    <div class="row my-2">
                                        <x-form-div class="col-xl-4">
                                            <x-input-span id="admitLabel">Admit?</x-input-span>
                                            <x-select-admit name="admit" :disabled="true"></x-select-admit>
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span id="wardLabel">Ward</x-input-span>
                                            <x-select-ward name="ward"></x-select-ward>
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span id="bedNumberLabel">Bed Number</x-input-span>
                                            <x-select-bed name="bedNumber"></x-select-bed>
                                        </x-form-div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="saveConsultationBtn" {!! $isReview ? 'data-btn="ancReview"' : 'data-btn="anc"' !!}
                                            class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Save
                                        </button>
                                    </div>
                                    <x-toast-successful  id="saveConsultationToast"></x-toast-successful>
                                </div>
                                <div class="d-none investigationAndManagementDiv" id="investigationAndManagementDiv{{ $isReview ? 'AncReview' : 'Anc' }}" {!! $isReview ? 'data-div="ancReview"' : 'data-div="anc"' !!}>
                                    <div class="mb-2 form-control">
                                        <x-form-span>Investigation & Management</x-form-span>
                                        <div class="row">
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="resourceLabel">Medical Resources</x-input-span>
                                                <x-form-input class="resource" type="search" name="resource" id="resource" data-input="anc" list="resourceListanc"
                                                    placeholder="search" />
                                                <datalist name="resource" type="text" class="decoration-none resourceList" id="resourceListanc"></datalist>
                                            </x-form-div>
                                            <x-form-div class="col-xl-6" id="pres">
                                                <x-input-span id="prescriptionLabel">Prescription</x-input-span>
                                                <x-form-input type="text" name="prescription" id="prescription"
                                                    placeholder="eg: 5mg BD x5" />
                                            </x-form-div>
                                            <x-form-div class="col-xl-6" id="qty">
                                                <x-input-span id="quantityLabel">Quantity</x-input-span>
                                                <x-form-input type="number" name="quantity" id="quantity" placeholder="" />
                                            </x-form-div>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="notesLabel">Note</x-input-span>
                                                <x-form-input type="text" name="note" id="note"/>
                                            </x-form-div>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" id="addInvestigationAndManagementBtn" data-btn="anc"
                                                class="btn btn-primary">
                                                add
                                                <i class="bi bi-prescription"></i>
                                            </button>
                                        </div>
                                        <x-toast-successful  id="saveInvestigationAndManagementToast"></x-toast-successful>
                                    </div>
                                    <div class="mb-2 form-control">
                                        <table id="prescriptionTable{{ $isReview ? 'ancReview' : 'anc' }}" class="table table-hover align-middle table-sm prescriptionTable">
                                            <thead>
                                                <tr>
                                                    <th>Prescribed</th>
                                                    <th>Resource</th>
                                                    <th>Prescription</th>
                                                    <th>Qty</th>
                                                    <th>Note</th>
                                                    <th>By</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
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
                </div>
            </div>
        </div>
    </div>
</div>
