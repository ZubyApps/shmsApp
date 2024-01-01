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
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Patient</x-input-span>
                                    <x-form-input name="patient" value="" id="patient"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Sponsor</x-input-span>
                                    <x-form-input name="sponsor" value="" id="sponsor"/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control vitalsDiv">
                            <x-form-span>All Prescriptions for this Visit</x-form-span>
                            <div class="row overflow-auto my-3">
                                <table id="visitPrescriptionsTable" class="table table-hover align-middle table-sm visitPrescriptionsTable mt-2">
                                    <thead>
                                        <tr>
                                            <th>Doctor</th>
                                            <th>Diagnosis</th>
                                            <th>Consulted</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                    <tfoot>
                                    </tfoot>
                                </table>
                            </div>
                            
                        </div>
            
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                    <button type="button" id="markDoneBtn" class="btn bg-primary text-white">
                        <i class="bi bi-check-circle me-1"></i>
                        Mark as Done
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
