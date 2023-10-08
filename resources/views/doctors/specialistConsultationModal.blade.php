<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="">
                        <div id="consultationDiv" data-div="specialist">
                            <div class="mb-2 form-control">
                                <x-form-label>Specialist Consultation</x-form-label>
                                <x-form-div class="col-xl-12">
                                    <x-input-span id="specialistDesignationLabel">Consultant Specialist<br />
                                        Name & Designation </x-input-span>
                                    <x-form-input name="consultantSpecialist" value="" autocomplete="on" />
                                </x-form-div>
                                <div class="row mt-2">
                                    <div class="row d-none addVitalsignsDiv" id="addVitalsignsDiv" data-div="specialist">
                                        <x-form-span class="fw-semibold">Vital Signs</x-form-span>
                                        @include('vitalsigns.vitalsigns', ['disabled' => true])
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="addVitalsignsBtn" data-btn="specialist" class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            vital signs
                                        </button>
                                    </div>
                                </div>
                                <div class="row">
                                    <x-form-span class="fw-semibold">Consultation</x-form-span>
                                    <x-form-div class="col-xl-12">
                                        <x-input-span id="presentingComplainLabel">Presenting <br />
                                            Complain</x-input-span>
                                        <x-form-textarea name="presentingComplain" id="presentingComplain"
                                            cols="10" rows="3"></x-form-textarea>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="historyOfPresentingComplainLabel">History of <br /> Presenting
                                            <br /> Complain</x-input-span>
                                        <x-form-textarea name="historyOfPresentingComplain"
                                            id="historyOfPresentingComplain" cols="10"
                                            rows="3"></x-form-textarea>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="medicalHistoryLabel">Past Medical/ <br /> Surgical
                                            History</x-input-span>
                                        <x-form-textarea name="medicalHistory" id="medicalHistory" cols="10"
                                            rows="3"></x-form-textarea>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="obGyneHistoryLabel">Obstetrics/<br />Gynecological <br />
                                            History</x-input-span>
                                        <x-form-textarea type="text" name="obGyneHistory" id="obGyneHistory"
                                            cols="10" rows="3"></x-form-textarea>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="examinationFindingsLabel">Examination <br />
                                            Findings</x-input-span>
                                        <x-form-textarea type="text" name="examinationFindings"
                                            id="examinationFindings" cols="10" rows="3"></x-form-textarea>
                                    </x-form-div>
                                    <x-form-div class="col-xl-12">
                                        <x-input-span>Search <br />ICD11 for Diagnosis</x-input-span>
                                        <x-icd11-diagnosis-input :number="3" />
                                    </x-form-div>
                                    <x-icd11-diagnosis-div :number="3" />
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="diagnosisLabel">Selected <br />ICD11 <br />
                                            Diagnosis</x-input-span>
                                        <x-form-textarea type="text" name="selectedDiagnosis"
                                            class="selectedDiagnosis-3" style="height: 100px"></x-form-textarea>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="diagnosisLabel">Addional <br /> Diagnosis</x-input-span>
                                        <x-form-textarea type="text" name="additionalDiagnosis"
                                            class="additionalDiagnosis" cols="10" rows="3"></x-form-textarea>
                                    </x-form-div>
                                    <x-form-div class="col-xl-12">
                                        <x-input-span id="physiciansPlanLabel">Physicians Plan</x-input-span>
                                        <x-form-textarea type="text" name="physiciansPlan" id="physiciansPlan"
                                            cols="10" rows="3"></x-form-textarea>
                                    </x-form-div>
                                </div>
                                <div class="row my-2">
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="admitLabel">Admit?</x-input-span>
                                        <select class="form-select form-select-md" name="admit">
                                            <option value="">Select</option>
                                            <option value="Outpatient">No</option>
                                            <option value="Inpatient">Yes</option>
                                        </select>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="wardLabel">Ward</x-input-span>
                                        <select class="form-select form-select-md" name="ward">
                                            <option value="">Select Ward</option>
                                            <option value="Private Ward">Private Ward</option>
                                            <option value="General Ward">General Ward</option>
                                        </select>
                                    </x-form-div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button" id="saveConsultationBtn" data-btn="specialist"
                                        class="btn bg-primary text-white">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="d-none" id="investigationAndManagementDiv" data-div="specialist">
                            <div class="mb-2 form-control">
                                <x-form-span>Investigation & Management</x-form-span>
                                <div class="row">
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="itemsLabel">Item</x-input-span>
                                        <x-form-input type="search" name="item" id="item"
                                            placeholder="search" list="itemsList" />
                                        <datalist name="item" type="text" class="decoration-none"
                                            id="itemsList"></datalist>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="prescriptionLabel">Prescription</x-input-span>
                                        <x-form-input type="text" name="prescription" id="prescription"
                                            placeholder="eg: 5mg BD x5" />
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="quantityLabel">Quantity</x-input-span>
                                        <x-form-input type="number" name="quantity" id="quantity"
                                            placeholder="" />
                                    </x-form-div>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button" id="addInvestigationAndManagmentBtn" data-btn="specialist"
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
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>12/09/2023 11:02pm</td>
                                            <td>N/S 500mls</td>
                                            <td>500mls 12hrly x2</td>
                                            <td></td>
                                            <td><button class="btn btn-outline-primary deleteBtn"><i
                                                        class="bi bi-trash"></i></button></td>
                                        </tr>
                                        <tr>
                                            <td>2</td>
                                            <td>12/09/2023 11:15pm</td>
                                            <td>5% mls Syringe</td>
                                            <td></td>
                                            <td>4</td>
                                            <td><button class="btn btn-outline-primary deleteBtn"><i
                                                        class="bi bi-trash"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
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
