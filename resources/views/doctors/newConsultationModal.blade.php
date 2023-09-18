<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">                 
                    <div class="">
                        <div class="mb-2">
                            <x-form-span>Bio</x-form-span>
                            <!-- first row -->
                            <div class="row">
                                <x-form-div class="">
                                    <x-input-span id="patientLabel">Patient</x-input-span>
                                    <x-form-input name="patientIds" readonly value="Patrick Abiodun Aso"/>
                                </x-form-div>
                                <x-form-div class="sponsorNameDiv">
                                    <x-input-span id="sponsorNameLabel">Sponsor Name</x-input-span>
                                    <x-form-input type="search" class="sponsorName" name="sponsorName" value="Axe Mansard" readonly/>
                                </x-form-div>
                                <x-form-div class="">
                                    <x-input-span>Age</x-input-span>
                                    <x-form-input name="age" class="age" value="49" readonly />
                                </x-form-div>
                                <x-form-div class="">
                                    <x-input-span>Sex</x-input-span>
                                    <x-form-input name="sex" class="Male" value="Male" readonly />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="maritalStatusLabel">Marital Status</x-input-span>
                                    <x-form-input name="sex" class="Male" value="Male" readonly />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span>Phone Number</x-input-span>
                                    <x-form-input type="tel" name="phoneNumber" id="phoneNumber" value="08034987761" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span >Ethnic Group</x-input-span>
                                    <x-form-input name="ethnicGroup" value="Yoruba" diabled/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <span class="fw-semibold">Previously Known Clinical Info</span>
                            <div class="row">
                                @include("patients.partials.known-clinical-info", ["readonly" => true])
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-form-span>Vital Signs</x-form-span>
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="bloodPressureLabel">Blood Pressur<x-required-span /></x-input-span>
                                    <x-form-input name="bloodPressure" id="bloodPressure" />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="temperatureLabel">Temparature</x-input-span>
                                    <x-form-input name="temperature" id="temperature" />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="sugarLevelLabel">Sugar Level<x-required-span /></x-input-span>
                                    <x-form-input name="sugarLevel" id="sugarLevel" />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="pulseRateLabel">Pulse Rate<x-required-span /></x-input-span>
                                    <x-form-input type="text" name="pulseRate" id="pulseRate" />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="respiratoryRateLabel">Respiratory Rate<x-required-span /></x-input-span>
                                    <x-form-input type="text" name="respiratoryRate" id="respiratoryRate" />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="weightLabel">Weight<x-required-span /></x-input-span>
                                    <x-form-input type="text" name="weight" id="weight" />
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="heightLabel">Height<x-required-span /></x-input-span>
                                    <x-form-input type="text" name="height" id="height" />
                                </x-form-div>
                                
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-form-span>Consultation</x-form-span>
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span id="presentingComplainLabel">Presenting <br/> Complain</x-input-span>
                                    <x-form-textarea name="presentingComplain" id="presentingComplain" cols="10" rows="3"></x-form-textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span id="historyOfPresentingComplainLabel">History of <br/> Presenting <br/> Complain</x-input-span>
                                    <x-form-textarea name="historyOfPresentingComplain" id="historyOfPresentingComplain" cols="10" rows="3"></x-form-textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span id="medicalHistoryLabel">Past Medical/ <br/> Surgical History</x-input-span>
                                    <x-form-textarea name="medicalHistory" id="medicalHistory" cols="10" rows="3"></x-form-textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span id="obGyneHistoryLabel">Obstetrics/<br/>Gynecological <br/> History</x-input-span>
                                    <x-form-textarea type="text" name="obGyneHistory" id="obGyneHistory" cols="10" rows="3"></x-form-textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span id="examinationFindingsLabel">Examination <br/> Findings</x-input-span>
                                    <x-form-textarea type="text" name="examinationFindings" id="examinationFindings" cols="10" rows="3"></x-form-textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span id="diagnosisLabel">Diagnosis</x-input-span>
                                    <x-form-textarea type="text" name="diagnosis" id="diagnosis" cols="10" rows="3"></x-form-textarea>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span id="physiciansPlanLabel">Physicians Plan</x-input-span>
                                    <x-form-textarea type="text" name="physiciansPlan" id="physiciansPlan" cols="10" rows="3"></x-form-textarea>
                                </x-form-div>
                            </div>
                            <div class="row">
                                <x-form-div>
                                    <x-input-span id="admitLabel">Admit?</x-input-span>
                                    <select class="form-select form-select-md"
                                            name="admit">
                                            <option value="">Select</option>
                                            <option value="Outpatient">No</option>
                                            <option value="Inpatient">Yes</option>
                                        </select>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="wardLabel">Ward</x-input-span>
                                    <select class="form-select form-select-md"
                                            name="ward">
                                            <option value="">Select Ward</option>
                                            <option value="Outpatient">Private Ward</option>
                                            <option value="Inpatient">General Ward</option>
                                        </select>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span id="physiciansPlanLabel">Bed No.</x-input-span>
                                    <select class="form-select form-select-md"
                                            name="bedNo">
                                            <option value="unknown">Select</option>
                                            <option value="Bed 1">Bed 1</option>
                                            <option value="Bed 2">Bed 2</option>
                                            <option value="Bed 3">Bed 3</option>
                                        </select>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <x-form-span>Investigation and Management</x-form-span>
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span id="productLabel">Product/Service</x-input-span>
                                    <x-form-input type="search" name="product" id="product" placeholder="search"  />
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span id="prescriptionLabel">Prescription</x-input-span>
                                    <x-form-input type="text" name="prescription" id="prescription" placeholder="eg: 5mg BD x5" />
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2">
                            <table id="prescriptionTable" class="table table-hover align-middle table-sm bg-primary">
                                <thead>
                                    <tr>
                                        <th>Billed at</th>
                                        <th>Item</th>
                                        <th>Prescription</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td><button class="btn btn-outline-primary deleteBtn"><i class="bi bi-trash"></i></button></td>
                                </tbody>
                                <tfoot class="fw-bolder text-primary">
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="button" id="{{ $isUpdate ? 'saveBtn' : 'registerBtn' }}" class="btn bg-primary text-white">
                        <i class="bi bi-check-circle me-1"></i>
                        {{ $isUpdate ? 'Update' : 'Register' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
