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
                                @include('patients.partials.PatientBio')
                            </div>
                            <div class="mb-2 form-control">
                                <X-form-span class="fw-semibold">Previously Known Clinical Info</X-form-span>
                                <div>
                                    <x-toast-successful class="col-xl-12"  id="knownClinicalInfoToast"></x-toast-successful>
                                    <div class="row">
                                        @include('patients.partials.known-clinical-info', ['disabled' => true])
                                    </div>   
                                </div>
                            </div>
                        </div>
                        <div id="visitHistoryDiv" data-bs-spy="scroll" tabindex="0" data-bs-smooth-scroll="true">
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer pe-4">
                    <a type="button" target="_blank" id="moreHistoryBtn" class="btn btn-primary me-auto">
                        More History
                        <i class=" bi bi-arrow-up-right-circle-fill"></i>
                    </a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>                                            
                </div>
            </div>
        </div>
    </div>
</div>
