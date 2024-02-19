<div class="container">
    <div class="modal fade " id="{{ $id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body reportModalBody">
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <header class="d-flex flex-wrap justify-content-end align-items-top mb-4 px-4">
                        <div class="text-right p-0">
                            <p>{{ date('F j, Y') }}</p>
                        </div>
                    </header>
                    <div class="px-4">
                        <p class="fs-6" name="recipientsAddress" id="recipientsAddress"></p>
                        <div class="text-center">
                            <label for="basic-url" class="form-label fs-4 fw-semibold" name="type" id="type"></label>
                        </div>
                        <div class="" id="medicalReportDiv">
                            <span class="fw-semibold">Patient:</span> <span class="text-decoration-underline" name="patientsInfo" id="patientsInfo"></span> <span class="text-decoration-underline" hidden name="patientsFullName" id="patientsFullName"></span>
                            <div class="row overflow-auto my-2">
                                <p class="" id="report" name="report"></p>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="">
                                <x-form-span class="fw-semibold">Signed By</x-form-span><br><br><br>
                                <span class="DoctorsName" name="doctor"></span><br>
                                <span><i class="DoctorsDesignation" name="designation"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="button" id="downloadReportBtn" class="btn btn-primary">
                        <i class="bi bi-download"></i>
                        Download
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
