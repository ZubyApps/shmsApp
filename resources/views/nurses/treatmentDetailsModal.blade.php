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
                        <div class="mb-2 form-control">
                            @include('patients.partials.patientBio')
                        </div>
                        <div class="mb-2 form-control">
                            <X-form-span class="fw-semibold">Previously Known Clinical Info</X-form-span>
                            <div class="row knownClinicalInfoDiv">
                                @include('patients.partials.known-clinical-info', ['disabled' => true])
                            </div>
                        </div>
                        @if ($isAnc)
                            <x-current-lmp-calculation></x-current-lmp-calculation>  
                        @endif
                        <div class="mb-2 form-control vitalsDiv {{ $isLab || $isHmo ? 'd-none' : '' }}">
                            <x-form-span>Vital Signs</x-form-span>
                            <div class="row overflow-auto my-3">
                                <table id="vitalSignsTableNurses{{ $isAnc ? 'AncConDetails' : 'ConDetails' }}" class="table table-hover align-middle table-sm vitalsTable">
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
                            <div class="row">
                                <div id="addVitalsignsDiv"  data-div="{{ $isAnc ? 'ancConDetails' : 'conDetails' }}">
                                    @if ($isAnc)
                                        @include('vitalsigns.ancVitalsigns', ['sf' => 'ancConDetails'])
                                    @else
                                        @include('vitalsigns.vitalsigns', ['sf' => 'conDetails'])
                                    @endif
                                    <x-toast-successful class="col-xl-12"  id="vitalSignsToast"></x-toast-successful>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button" id="addVitalsignsBtn"  data-btn="{{ $isAnc ? 'ancConDetails' : 'conDetails' }}"
                                        class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="treatmentDiv">
                        </div>
                        @if ($isAnc)
                        <x-bill-summary :number="'AncConDetails'" class="{{ $isLab ? 'd-none' : '' }}"></x-bill-summary> @else <x-bill-summary :number="'ConDetails'" class="{{ $isLab ? 'd-none' : '' }}"></x-bill-summary> @endif
                        {{-- <x-bill-summary class="{{ $isLab ? 'd-none' : '' }}"></x-bill-summary> --}}
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    {{-- <button type="button" id="saveBtn" class="btn bg-primary text-white">
                        <i class="bi bi-check-circle me-1"></i>
                        Save
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
</div>
