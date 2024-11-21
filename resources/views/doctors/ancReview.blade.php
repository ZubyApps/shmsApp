<div class="form-control">
    <x-form-label class="text-primary">ANC Review</x-form-label>
    <div class="mb-2 form-control vitalsDiv">
        <x-form-span>Vital Signs</x-form-span>
        <div class="row overflow-auto my-3">
            <table id="vitalSignsTableAncReviewDiv" class="table table-hover align-middle table-sm vitalsTable">
                <thead>
                    <tr>
                        <th>Done</th>
                        <th>BP</th>
                        <th>Weight</th>
                        <th>Urine-Protein</th>
                        <th>Urine-Glucose</th>
                        <th>Remarks</th>
                        <th>By</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <div class="row">
            <div id="addVitalsignsDiv" data-div="ancReviewDiv">
                @include('vitalsigns.ancVitalsigns', ['sf' => 'AncReviewDiv' ])
                <x-toast-successful class="col-xl-12"  id="vitalSignsToast"></x-toast-successful>
            </div>
            <div class="d-flex justify-content-center">
                <button type="button" id="addVitalsignsBtn" data-btn="ancReviewDiv"
                    class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>
                    add
                </button>
            </div>
        </div>
    </div>
    <div class="consultationParentDiv">
        <div class="mb-2 form-control " id="consultationDiv" data-div="ancReviewDiv">
            <x-form-label>Consultation {{ $isReview ? "Review" : '' }}</x-form-label>
            <div class="row">
                <x-form-div class="col-xl-12">
                    <x-input-span id="specialistDesignationLabel">Consultant (Name&Designation) </x-input-span>
                    <x-form-input name="consultantSpecialist" value=""
                        placeholder="if applicable..." />
                </x-form-div>
                <x-form-div class="col-xl-4">
                    <x-input-span>LMP</x-input-span>
                    <x-form-input type="date" name="lmp" id="lmp" data-lmp="ancReviewDiv" />
                </x-form-div>
                <x-form-div class="col-xl-4">
                    <x-input-span>EDD</x-input-span>
                    <x-form-input type="date" name="edd" id="edd" readonly/>
                </x-form-div>
                <x-form-div class="col-xl-4">
                    <x-input-span>EGA</x-input-span>
                    <x-form-input name="ega" id="ega" readonly/>
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
                    <x-input-span id="remarksLabel">Remarks </x-input-span>
                    <x-form-textarea type="text" name="remarks" class="remarks" cols="10" rows="2"></x-form-textarea>
                </x-form-div>
                <x-form-div class="col-xl-12">
                    <x-input-span>Search <br />for ICD11 Diagnosis</x-input-span>
                    <x-icd11-diagnosis-input :number="6" />
                </x-form-div>
                <x-icd11-diagnosis-div :number="6" /> 
                <x-form-div class="col-xl-6">
                    <x-input-span id="selectedDiagnosisLabel">Selected <br />ICD11 Diagnosis<x-required-span />
                        <i class="bi bi-arrow-clockwise btn form-control clearDiagnosis"></i></x-input-span>
                    <x-form-textarea type="text" name="selectedDiagnosis"
                        class="selectedDiagnosis-6" id="selectedDiagnosis" readonly></x-form-textarea>
                </x-form-div>
                {{-- @if ($isReview) --}}
                <x-form-div class="col-xl-6">
                    <x-input-span id="diagnosisLabel">Provisional <br /> Diagnosis</x-input-span>
                    <x-form-textarea type="text" name="provisionalDiagnosis" class="provisionalDiagnosis" id="provisionalDiagnosis" cols="10" rows="2"></x-form-textarea>
                </x-form-div>
                {{-- @endif --}}
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
            @include('extras.wardAndBedDiv', ['condition' => true])
            <div class="d-flex justify-content-center">
                <button type="button" id="saveConsultationBtn" data-btn="ancReviewDiv"
                    class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>
                    Save
                </button>
            </div>
            <x-toast-successful  id="saveConsultationToast"></x-toast-successful>
        </div>
        @include('extras.investigationAndManagementDiv', ['type' => 'AncReviewDiv'])
    </div>
</div>