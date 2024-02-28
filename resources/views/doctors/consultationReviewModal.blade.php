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
                        <div class="card card-body">
                            <div class="mb-2 form-control">
                                @include('patients.partials.patientBio')
                            </div>
                            <div class="mb-2 form-control">
                                <X-form-span class="fw-semibold">Previously Known Clinical Info</X-form-span>
                                <div id="knownClinicalInfoDiv" data-div="{{ $isAnc ? 'ancConReview' : 'conReview' }}">
                                    <x-toast-successful class="col-xl-12"  id="knownClinicalInfoToast"></x-toast-successful>
                                    <div class="row">
                                        @include('patients.partials.known-clinical-info', ['disabled' => true])
                                        <div class="d-flex justify-content-center">
                                            <button type="button" id="updateKnownClinicalInfoBtn"
                                            class="btn bg-primary text-white" data-btn="{{ $isAnc ? 'ancConReview' : 'conReview' }}">
                                            Update
                                            </button>
                                        </div>
                                    </div>   
                                </div>
                            </div>
                            @if ($isAnc)
                            <x-current-lmp-calculation></x-current-lmp-calculation>  
                            @endif
                            <div class="mb-2 form-control">
                                <x-form-span>Vital Signs</x-form-span>
                                <div class="row overflow-auto my-3">
                                    <table id="vitalSignsConsultation{{ $isAnc ? 'AncConReview' : 'ConReview' }}"
                                        class="table table-hover align-middle table-sm bg-primary vitalSignsTable">
                                        <thead>
                                            @if ($isAnc)
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
                                            @else
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
                                            @endif
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                                <div class="row d-none">
                                    <div id="addVitalsignsDiv" data-div="{{ $isAnc ? 'ancConReview' : 'conReview' }}">
                                        @if ($isAnc)
                                            @include('vitalsigns.ancVitalsigns', ['sf' => 'ancConReview'])
                                        @else
                                            @include('vitalsigns.vitalsigns', ['sf' => 'conReview'])
                                        @endif
                                        <x-toast-successful class="col-xl-12"  id="vitalSignsToast"></x-toast-successful>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="addVitalsignsBtn" data-btn="{{ $isAnc ? 'ancConReview' : 'conReview' }}"
                                            class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            add
                                        </button>
                                    </div>
                                </div>
                                <div class="overflow-auto">
                                    <div class="chart-container" style="position: relative; height:60vh; width:90vw">
                                        <canvas id="vitalsignsChart{{ $isAnc ? 'AncConReview' : 'ConReview' }}" class="vitalsignsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="consultationReviewDiv" data-bs-spy="scroll" tabindex="0" data-bs-smooth-scroll="true">
                        </div>
                        @if ($isAnc)
                        <x-bill-summary :number="'AncConReview'"></x-bill-summary> @else <x-bill-summary :number="'ConReview'"></x-bill-summary> @endif
                    </div>
                </div>
                <div class="modal-footer pe-4">
                    @if ($isAnc)
                        <button type="button" id="reviewAncPatientBtn" class="btn btn-primary me-auto reviewConBtns">
                            <i class="bi bi-plus-circle me-1"></i>
                            Anc Review
                        </button>
                    @else
                        <button type="button" id="reviewPatientBtn" class="btn btn-primary reviewConBtns">
                            <i class="bi bi-plus-circle me-1"></i>
                            Review
                        </button>
                        <button type="button" id="specialistConsultationBtn" class="btn btn-primary me-auto reviewConBtns">
                            <i class="bi bi-plus-circle me-1"></i>
                            Specialist Consultation
                        </button>
                    @endif
                    <button type="button" id="dischargeBtn" class="btn btn-primary reviewConBtns">
                        <i class="ms-1 bi bi-arrow-up-right-circle-fill"></i>
                        Discharge
                    </button>    
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>                                            
                </div>
            </div>
        </div>
    </div>
</div>
