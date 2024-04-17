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
                        <div class="mb-2 form-control">
                            <div class="row">
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Patient</x-input-span>
                                    <x-form-input name="patient" value="" id="patient"/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Sponsor</x-input-span>
                                    <x-form-input name="sponsorName" value="" id="sponsorName"/>
                                </x-form-div>
                            </div>
                        </div>
                        @if ($isMedications)
                        <button type="button" class="btn btn-primary addPrescriptionBtn {{ $isDoctor ? 'd-none' : '' }}">
                            <i class="bi bi-plus-circle me-1"></i>
                            Emergency Prescription
                        </button>
                            <div class="my-2 form-control">
                                <span class="fw-bold text-primary">Injectables & Infusions</span>
                                <div class="row overflow-auto m-1">
                                    <table id="medicationsTable" class="table table-sm medicationsTable">
                                        <thead>
                                            <tr>
                                                <th>Treatment</th>
                                                <th>Prescription</th>
                                                <th>Qty</th>
                                                <th>Dr</th>
                                                <th>Prescribed</th>
                                                <th>Note</th>
                                                <th>Chartable</th>
                                                <th>Chart</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            <div class="my-2 form-control">
                                <span class="fw-bold text-primary">Other Prescriptions</span>
                                <div class="row overflow-auto m-1">
                                    <table id="otherPrescriptionsTable" class="table table-sm otherPrescriptionsTable">
                                        <thead>
                                            <tr>
                                                <th>Item</th>
                                                <th>Note/Instruction</th>
                                                <th>Qty</th>
                                                <th>Dr</th>
                                                <th>Prescribed</th>
                                                <th>Chartable</th>
                                                <th>Chart</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer px-5">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
