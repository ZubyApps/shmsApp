<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body resultModalBody">
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="text-left p-0 px-4 d-none addressDiv">
                        <h4 class="">Sandra Hospital Makurdi</h4>
                        <p>24 J.S Takar Way</p>
                        <p>Makurdi, Benue State</p>
                    </div>
                    <header class="d-flex flex-wrap justify-content-start align-items-top mb-4 px-4">
                        <div class="text-right p-0">
                            <p id="resultDate"></p>
                        </div>
                    </header>
                    <div class="px-4">
                        {{-- <p class="fs-6" name="recipientsAddress" id="recipientsAddress"></p> --}}
                        <span class="fw-semibold">Patient:</span> <span class="text-decoration-underline" name="patientsId" id="patientsId"></span>
                        <div class="text-center">
                            <div for="basic-url" class="form-label fs-4 mainLabel" name="mainLabel" id="mainLabel">Laboratory Test Result(s)</div>
                        </div>
                        <div class="" id="medicalReportDiv">
                            <div class="row overflow-auto my-2 testListDiv">
                                <div class="fw-semibold" name="test" id="test"></div> <p class="" id="result" name="result"></p>
                            </div>
                            <div class="row overflow-auto my-2 multipleTestsListDiv"></div>
                        </div>
                        <div class="row mt-3">
                            <div class="signedByDiv">
                                <x-form-span class="fw-semibold">Signed By</x-form-span><br><br>
                                <span class="staffFullName fw-semibold" name="StaffFullName" id="StaffFullName"></span><br>
                                {{-- <span>Laboratory Technician</span> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="button" id="downloadResultBtn" class="btn btn-primary">
                        <i class="bi bi-download"></i>
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
