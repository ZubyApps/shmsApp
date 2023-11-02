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
                                <div id="knownClinicalInfoDiv" data-div="conReview">
                                    <x-toast-successful class="col-xl-12"  id="knownClinicalInfoToast"></x-toast-successful>
                                    <div class="row">
                                        @include('patients.partials.known-clinical-info', ['disabled' => true])
                                        <div class="d-flex justify-content-center">
                                            <button type="button" id="updateKnownClinicalInfoBtn"
                                                class="btn bg-primary text-white" data-btn="conReview">
                                                Update
                                            </button>
                                        </div>
                                    </div>   
                                </div>
                            </div>
                            <div class="mb-2 form-control">
                                <x-form-span>Vital Signs</x-form-span>
                                <div class="row overflow-auto my-3">
                                    <table id="vitalSignsConsultationReview"
                                        class="table table-hover align-middle table-sm bg-primary">
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
                                <div class="row d-none">
                                    <div class="row" id="addVitalsignsDiv" data-div="conReview">
                                        @include('vitalsigns.vitalsigns', ['disabled' => false])
                                        <x-toast-successful class="col-xl-12"  id="vitalSignsToast"></x-toast-successful>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" id="addVitalsignsBtn" data-btn="conReview"
                                            class="btn btn-primary">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            add
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="consultationReviewDiv">
                        </div>
                    </div>
                </div>
                <div class="modal-footer pe-4">    
                    <button type="button" id="reviewPatientBtn" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-1"></i>
                        Review
                    </button>
                    <button type="button" id="specialistConsultationBtn" class="btn btn-primary me-auto">
                        <i class="bi bi-plus-circle me-1"></i>
                        Specialist Consultation
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
