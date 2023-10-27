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
                        <div id="consultationDiv" data-div="review">
                            <div class="mb-2 form-control">
                                <x-form-label>Review Patient</x-form-label>
                                <div class="row">
                                    <div class="d-none row" id="addVitalsignsDiv" data-div="review">
                                        <x-form-span class="fw-semibold">Vital Signs</x-form-span>
                                        @include('vitalsigns.vitalsigns', ['disabled' => false])
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="addVitalsignsBtn" data-btn="review"
                                            class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            vital signs
                                        </button>
                                    </div>
                                </div>
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
                                        <x-input-span id="diagnosisLabel">Updated <br />ICD11 <br />
                                            Diagnosis</x-input-span>
                                        <x-form-textarea type="text" name="selectedDiagnosis"
                                            class="selectedDiagnosis-2" style="height: 100px"></x-form-textarea>
                                    </x-form-div>
                                    <x-form-div class="col-xl-6">
                                        <x-input-span class="" id="assessmentLabel"> Assessment
                                        </x-input-span>
                                        <x-form-textarea class="assessment" name="assessment" cols="10"
                                            rows="3"></x-form-textarea>
                                    </x-form-div>
                                    <x-form-div class="col-xl-12">
                                        <x-input-span class="" id="planLabel">Plan</x-input-span>
                                        <x-form-textarea class="" name="plan" id="plan" cols="10"
                                            rows="3"></x-form-textarea>
                                    </x-form-div>
                                </div>
                                <div class="row my-2">
                                    <x-form-div class="col-xl-4">
                                        <x-input-span id="admitLabel">Admit?</x-input-span>
                                        <select class="form-select form-select-md" name="admit">
                                            <option value="">Select</option>
                                            <option value="out-patient">No</option>
                                            <option value="in-patient">Yes</option>
                                            <option value="observation">Observation</option>
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
                                    <button type="button" id="saveConsultationBtn" data-btn="review"
                                        class="btn bg-primary text-white">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Save
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="d-none" id="investigationAndManagementDiv" data-div="review">
                            <div class="mb-2 form-control">
                                <x-form-span>Investigation & Management</x-form-span>
                                <div class="row">
                                    <x-form-div class="col-xl-6">
                                        <x-input-span id="itemsLabel">Item</x-input-span>
                                        <x-form-input type="search" name="item" id="item" placeholder="search"
                                            list="itemsList" />
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
                                    <button type="button" id="addInvestigationAndManagmentBtn" data-btn="review"
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
