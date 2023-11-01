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
                                <div class="mb-2 form-control">
                                    <x-form-span>Vital Signs</x-form-span>
                                    <div class="row overflow-auto m-1">
                                        <table id="prescriptionTable"
                                            class="table table-hover align-middle table-sm bg-primary">
                                            <thead>
                                                <tr>
                                                    <th>Temperature</th>
                                                    <th>Blood Pressure</th>
                                                    <th>Pulse Rate</th>
                                                    <th>Respiratory Rate</th>
                                                    <th>Weight</th>
                                                    <th>Height</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>37C</td>
                                                    <td>120/80mmgh</td>
                                                    <td>90</td>
                                                    <td>32</td>
                                                    <td>94kg</td>
                                                    <td>1.5m</td>
                                                </tr>
                                                <tr>
                                                    <td>37.1C</td>
                                                    <td>110/80mmgh</td>
                                                    <td>96</td>
                                                    <td>40</td>
                                                    <td>94kg</td>
                                                    <td>1.5m</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="row">
                                        <div class="row d-none" id="addVitalsignsDiv" data-div="anc">
                                            @include('vitalsigns.vitalsigns', ['disabled' => true])
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" id="addVitalsignsBtn" data-btn="anc"
                                                class="btn btn-primary">
                                                <i class="bi bi-plus-circle me-1"></i>
                                                add
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <div class="consultationParentDiv">
                                <div class="mb-2 form-control " id="consultationDiv" data-div="anc">
                                    <x-form-label>Consultation</x-form-label>
                                    <x-form-div class="col-xl-12">
                                        <x-input-span id="specialistDesignationLabel">Consultant Specialist<br />
                                            Name & Designation </x-input-span>
                                        <x-form-input name="consultantSpecialist" value=""
                                            placeholder="if applicable..." />
                                    </x-form-div>
                                    <div class="row">
                                        <x-form-div class="col-xl-3">
                                            <x-input-span>LMP</x-input-span>
                                            <x-form-input type="date" name="lmp" value="" />
                                        </x-form-div>
                                        <x-form-div class="col-xl-3">
                                            <x-input-span>EDD</x-input-span>
                                            <x-form-input type="date" name="edd" value="" />
                                        </x-form-div>
                                        <x-form-div class="col-xl-3">
                                            <x-input-span>EGA</x-input-span>
                                            <x-form-input name="ega" value="" />
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="obGyneHistoryLabel">Obstetrics/<br />Gynecological History</x-input-span>
                                            <x-form-textarea type="text" name="obGynHistory" id="obGynHistory"
                                                cols="10" rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="presentingComplainLabel">Ultrasound <br />
                                                Report</x-input-span>
                                            <x-form-textarea name="ultrasoundReport" id="presentingComplain"
                                                cols="10" rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span>Fetal <br> Heart Rate</x-input-span>
                                            <x-form-input type="text" name="fetalHeartRate" value="" />
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span>Height <br> of Fondus</x-input-span>
                                            <x-form-input type="text" name="heighOfFundus" value="" />
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="pastMedicalHistoryLabel">Presentation <br /> And
                                                Position</x-input-span>
                                            <x-form-textarea name="pastMedicalHistory" id="pastMedicalHistory"
                                                cols="10" rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="examinationFindingsLabel">Relation of <br>
                                                Presenting Part to Brim</x-input-span>
                                            <x-form-textarea type="text" name="relationOfPresentingPartToBrim"
                                                id="relationOfPresentingPartToBrim" cols="10"
                                                rows="3"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-12">
                                            <x-input-span>Search <br />for ICD11 Diagnosis</x-input-span>
                                            <x-icd11-diagnosis-input :number="4" />
                                        </x-form-div>
                                        <x-icd11-diagnosis-div :number="4" />
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="diagnosisLabel">Selected <br />ICD11
                                                Diagnosis</x-input-span>
                                            <x-form-textarea type="text" name="selectedDiagnosis"
                                                class="selectedDiagnosis-4"></x-form-textarea>
                                        </x-form-div>
                                        <x-form-div class="col-xl-6">
                                            <x-input-span id="diagnosisLabel">Remarks </x-input-span>
                                            <x-form-textarea type="text" name="additionalDiagnosis"
                                                class="additionalDiagnosis" cols="10"
                                                rows="3"></x-form-textarea>
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
                                            <select class="form-select form-select-md" name="admit">
                                                <option value="">Select</option>
                                                <option value="Outpatient">No</option>
                                                <option value="Inpatient">Yes</option>
                                                <option value="Observation">Observation</option>
                                            </select>
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span id="wardLabel">Ward</x-input-span>
                                            <select class="form-select form-select-md" name="ward">
                                                <option value="">Select Ward</option>
                                                <option value="FW">Female Ward</option>
                                                <option value="MW">Male Ward</option>
                                                <option value="PW 1">Private Ward 1</option>
                                                <option value="PW 2">Private Ward 2</option>
                                                <option value="PW 3">Private Ward 3</option>
                                                <option value="PW 4">Private Ward 4</option>
                                                <option value="PW 5">Private Ward 5</option>
                                                <option value="PW 6">Private Ward 6</option>
                                                <option value="Old Ward">Old Ward</option>
                                            </select>
                                        </x-form-div>
                                        <x-form-div class="col-xl-4">
                                            <x-input-span id="bedNumberLabel">Bed Number</x-input-span>
                                            <select class="form-select form-select-md" name="bedNumber">
                                                <option value="">Select Bed</option>
                                                <option value="Bed 1">Bed 1</option>
                                                <option value="Bed 2">Bed 2</option>
                                                <option value="Bed 3">Bed 3</option>
                                            </select>
                                        </x-form-div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="saveConsultationBtn" data-btn="anc"
                                            class="btn bg-primary text-white">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Save
                                        </button>
                                    </div>
                                    <x-toast-successful  id="saveConsultationToast"></x-toast-successful>
                                </div>
                                <div class="d-none investigationAndManagementDiv" id="investigationAndManagementDivAnc" data-div="anc">
                                    <div class="mb-2 form-control">
                                        <x-form-span>Investigation & Management</x-form-span>
                                        <div class="row">
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="itemLabel">Item</x-input-span>
                                                <x-form-input type="search" name="item" id="item"
                                                    placeholder="search" />
                                                <datalist name="item" type="text"
                                                    class="decoration-none"></datalist>
                                            </x-form-div>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="prescriptionLabel">Prescription</x-input-span>
                                                <x-form-input type="text" name="prescription" id="prescription"
                                                    placeholder="eg: 5mg BD x5" />
                                            </x-form-div>
                                            <x-form-div class="col-xl-6">
                                                <x-input-span id="quantityLabel">Quantity</x-input-span>
                                                <x-form-input type="number" name="quantity" id="quantity" placeholder="" />
                                            </x-form-div>
                                        </div>
                                        <div class="d-flex justify-content-center">
                                            <button type="button" id="addInvestigationAndManagmentBtn" data-btn="anc"
                                                class="btn btn-primary">
                                                add
                                                <i class="bi bi-prescription"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mb-2 form-control">
                                        <table id="prescriptionTable"
                                            class="table table-hover align-middle table-sm bg-primary">
                                            <thead>
                                                <tr>
                                                    <th>S/N</th>
                                                    <th>Billed at</th>
                                                    <th>Item</th>
                                                    <th>Prescription</th>
                                                    <th>Qty</th>
                                                    <th>Actions</th>
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
