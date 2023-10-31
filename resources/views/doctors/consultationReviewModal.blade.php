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
                                            {{-- <i class="bi bi-arrow-up-circle"></i> --}}
                                            Update
                                        </button>
                                    </div>
                                </div>   
                            </div>
                        </div>
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
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="row d-none" id="addVitalsignsDiv" >
                                    @include('vitalsigns.vitalsigns', ['disabled' => false])
                                </div>
                                <div class="d-flex justify-content-center d-none">
                                    <button type="button" id="addVitalsignsBtn" 
                                        class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        add
                                    </button>
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
