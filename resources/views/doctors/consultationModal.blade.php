    <div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div class="mb-2 form-control">
                            @include('patients.partials.PatientBio')
                        </div>
                        <div class="mb-2 form-control">
                            <X-form-span class="fw-semibold">Previously Known Clinical Info</X-form-span>
                            <div id="knownClinicalInfoDiv" {!! $isSpecialist ? 'data-div="specialist"' : 'data-div="new"' !!}>
                                <x-toast-successful class="col-xl-12"  id="knownClinicalInfoToast"></x-toast-successful>
                                <div class="row">
                                    @include('patients.partials.known-clinical-info', ['disabled' => true])
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="updateKnownClinicalInfoBtn" class="btn bg-primary text-white" {!! $isSpecialist ? 'data-btn="specialist"' : 'data-btn="new"' !!}>
                                            Update
                                        </button>
                                    </div>
                                </div>                              
                            </div>
                        </div>
                        <div class="card card-body">
                            <div class="mb-2 form-control vitalsDiv">
                                <x-form-span>Vital Signs</x-form-span>
                                <div class="row overflow-auto my-3">
                                    <table id="vitalSignsTable{{ $isSpecialist ? 'Specialist' : 'New' }}" class="table table-hover align-middle table-sm vitalsTable">
                                        <thead>
                                            <tr>
                                                <th>Done</th>
                                                <th>Temp</th>
                                                <th>BP</th>
                                                <th>Pulse</th>
                                                <th>Resp Rate</th>
                                                <th>SpO2</th>
                                                <th>Weight</th>
                                                <th>Height</th>
                                                <th>BMI</th>
                                                <th>Head Circ</th>
                                                <th>Mid A Circ</th>
                                                <th>Fluid Drain</th>
                                                <th>Urine OutPut</th>
                                                <th>Fetal HR</th>
                                                <th>Note</th>
                                                <th>By</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div id="addVitalsignsDiv" {!! $isSpecialist ? 'data-div="specialist"' : 'data-div="new"' !!}>
                                        @include('vitalsigns.vitalsigns', [ 'sf' => $isSpecialist ? 'Specialist' : 'New' ])
                                        <x-toast-successful class="col-xl-12"  id="vitalSignsToast"></x-toast-successful>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="addVitalsignsBtn" {!! $isSpecialist ? 'data-btn="specialist"' : 'data-btn="new"' !!}
                                            class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            add
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="consultationParentDiv">
                                <div class="mb-2 form-control" id="consultationDiv" {!! $isSpecialist ? 'data-div="specialist"' : 'data-div="new"' !!}>
                                    <x-form-label>Consultation</x-form-label>
                                    <div class="row">
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="specialistDesignationLabel">Consultant Specialist<br />
                                                Name & Designation </x-input-span>
                                            <x-form-input name="consultantSpecialist" placeholder="if applicable..." />
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="presentingComplainLabel">Presenting <br /> Complain</x-input-span>
                                            <x-form-textarea name="presentingComplain" id="presentingComplain" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="historyOfPresentingComplainLabel">History of <br /> Presenting Complain</x-input-span>
                                            <x-form-textarea name="historyOfPresentingComplain" id="historyOfPresentingComplain" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="pastMedicalHistoryLabel">Past Medical/ <br /> Surgical History</x-input-span>
                                            <x-form-textarea name="pastMedicalHistory" id="pastMedicalHistory" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-12">
                                            <x-input-span id="paediatricHistoryLabel">Paedetric History</x-input-span>
                                            <x-form-textarea name="paediatricHistory" id="paediatricHistory" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="obGyneHistoryLabel">Obstetrics/<br />Gynecological History</x-input-span>
                                            <x-form-textarea type="text" name="obGynHistory" id="obGynHistory" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="examinationFindingsLabel">Examination <br /> Findings</x-input-span>
                                            <x-form-textarea type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-12">
                                            <x-input-span>Search <br />for ICD11 Diagnosis</x-input-span>
                                            @if($isSpecialist) <x-icd11-diagnosis-input :number="3" /> @else <x-icd11-diagnosis-input :number="1" /> @endif  
                                        </x-form-div>
                                        @if ($isSpecialist) <x-icd11-diagnosis-div :number="3" /> @else <x-icd11-diagnosis-div :number="1" /> @endif
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="diagnosisLabel">Selected <br />ICD11 Diagnosis<x-required-span />
                                                <i class="bi bi-arrow-clockwise btn form-control clearDiagnosis" ></i></x-input-span>
                                            <x-form-textarea type="text" name="selectedDiagnosis" class="selectedDiagnosis-{{ $isSpecialist ? '3' : '1' }}" id="selectedDiagnosis" readonly></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="diagnosisLabel">Provisional <br /> Diagnosis</x-input-span>
                                            <x-form-textarea type="text" name="provisionalDiagnosis" class="provisionalDiagnosis" id="provisionalDiagnosis" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="historyOfCareLabel">History of care</x-input-span>
                                            <x-form-textarea type="text" name="historyOfCare" id="historyOfCare" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="physiciansPlanLabel">Physicians Plan</x-input-span>
                                            <x-form-textarea type="text" name="plan" id="plan" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                    </div>
                                    <div class="row my-2">
                                        <x-form-div class="col-xl-4">
                                            <x-input-span id="admitLabel">Admit?<x-required-span /></x-input-span>
                                            <x-select-admit name="admit" id="admit" :disabled="false"></x-select-admit>
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
                                    @if ($isSpecialist)
                                    <div class="row d-none">
                                        <x-form-div class="col-xl-12">
                                            <x-input-span id="specialistLabel">Specialist Consultation Confirmation</x-input-span>
                                            <x-form-input type="text" name="specialConsultation" id="specialConsultation" value="1" />
                                        </x-form-div>
                                    </div>
                                    @endif
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="saveConsultationBtn" {!! $isSpecialist ? 'data-btn="specialist"' : 'data-btn="new"' !!}
                                            class="btn btn-primary">
                                            Save
                                        </button>
                                    </div>
                                    <x-toast-successful  id="saveConsultationToast"></x-toast-successful>
                                </div>
                                @include('extras.investigationAndManagementDiv', ['type' => $isSpecialist ? 'Specialist' : 'New', 'isNurse' => false])
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-primary historyBtn">
                        <i class=" bi bi-arrow-up-left-circle-fill"></i>
                        Consultation History
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
