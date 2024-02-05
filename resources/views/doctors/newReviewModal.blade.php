<div class="container">
    <div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
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
                            <div id="knownClinicalInfoDiv" data-div="review">
                                <x-toast-successful class="col-xl-12"  id="knownClinicalInfoToast"></x-toast-successful>
                                <div class="row">
                                    @include('patients.partials.known-clinical-info', ['disabled' => true])
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="updateKnownClinicalInfoBtn"
                                            class="btn bg-primary text-white" data-btn="review">
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
                                    <table id="vitalSignsTableReview" class="table table-hover align-middle table-sm vitalsTable">
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
                                                <th>Note</th>
                                                <th>By</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div id="addVitalsignsDiv" data-div="review">
                                        @include('vitalsigns.vitalsigns', ['sf' => 'review',])
                                        <x-toast-successful class="col-xl-12"  id="vitalSignsToast"></x-toast-successful>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="addVitalsignsBtn"  data-btn="review"
                                            class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            add
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="consultationParentDiv">
                                <div class="mb-2 form-control" id="consultationDiv" data-div="review">
                                    <x-form-span class="fw-semibold fs-5">Consultation Review</x-form-span>
                                    <div class="row">
                                        <x-form-div class="col-xl-12">
                                            <x-input-span id="specialistDesignationLabel">Consultant Specialist<br />
                                                Name & Designation </x-input-span>
                                            <x-form-input name="consultantSpecialist" value=""
                                                placeholder="if applicable..." />
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="complaintLabel">Complaint</x-input-span>
                                            <x-form-textarea name="complaint" id="complaint" cols="10"
                                                rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span class="" id="notesLabel"> Notes </x-input-span>
                                            <x-form-textarea class="" name="notes" id="notes" cols="10"
                                                rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-12">
                                            <x-input-span class="" id="examinationFindingsLabel"> Exam Findings </x-input-span>
                                            <x-form-textarea name="examinationFindings" id="examinationFindings"
                                                cols="10" rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-12">
                                            <x-input-span>Search <br />for ICD11 Diagnosis</x-input-span>
                                            <x-icd11-diagnosis-input :number="2" />
                                        </x-form-div>
                                        <x-icd11-diagnosis-div :number="2" />
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="diagnosisLabel">Selected <br />ICD11
                                                Diagnosis<x-required-span /><i class="bi bi-arrow-clockwise btn form-control clearDiagnosis"></i></x-input-span>
                                            <x-form-textarea type="text" name="selectedDiagnosis" id="selectedDiagnosis"
                                                class="selectedDiagnosis-2" ></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="diagnosisLabel">Provisional <br /> Diagnosis</x-input-span>
                                            <x-form-textarea type="text" name="provisionalDiagnosis" class="provisionalDiagnosis" id="provisionalDiagnosis" cols="10" rows="2"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span class="" id="assessmentLabel"> Assessment
                                            </x-input-span>
                                            <x-form-textarea class="assessment" name="assessment" id="assessment" cols="10"
                                                rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span class="" id="planLabel">Plan</x-input-span>
                                            <x-form-textarea class="" name="plan" id="plan" cols="10"
                                                rows="3"></x-form-textarea>
                                        </x-form-div>
                                    </div>
                                    <div class="row my-2">
                                        <x-form-div class="col-xl-4">
                                            <x-input-span id="admitLabel">Admit?</x-input-span>
                                            <x-select-admit name="admit" :disabled="false"></x-select-admit>
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
                                        <button type="button" id="saveConsultationBtn" data-btn="review"
                                            class="btn btn-primary text-white">
                                            Save
                                        </button>
                                    </div>
                                    <x-toast-successful  id="saveConsultationToast"></x-toast-successful>
                                </div>
                                @include('extras.investigationAndManagementDiv', ['type' => 'Review'])
                            </div> 
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-2">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
