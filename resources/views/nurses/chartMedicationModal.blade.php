<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                        <div class="mb-2 form-control">
                            <x-form-label>Chart Medication</x-form-label>
                            <div class="row">
                                <x-form-div>
                                    <x-input-span>Patient</x-input-span>
                                    <x-form-input name="patient" value="" id="patient" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span>Sponsor</x-input-span>
                                    <x-form-input name="sponsorName" value="" id="sponsorName" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span>Treatment</x-input-span>
                                    <x-form-input type="text" name="treatment" value="" id="treatment" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span>Prescription</x-input-span>
                                    <x-form-input name="prescription" value="" id="prescription" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span>Prescribed By</x-input-span>
                                    <x-form-input type="text" name="prescribedBy" value="" id="prescribedBy" readonly/>
                                </x-form-div>
                                <x-form-div>
                                    <x-input-span>Prescribed</x-input-span>
                                    <x-form-input type="datetime-local" name="prescribed" value="" id="prescribed" readonly/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control">
                            <X-form-div class="py-4">
                                <table id="medicationChartTable" class="table table-hover align-middle table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>Dose</th>
                                            <th>Scheduled Time</th>
                                            <th>Charted By</th>
                                            <th>Charted At</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </X-form-div>
                            <div class="row" id="chartMedicationDiv" data-div="chart">
                                <x-form-span class="mb-2 fw-semibold">Save the doses and time intervals according to prescription</x-form-span>
                                <x-form-div class="col-xl-4">
                                    <x-input-span>Dose<x-required-span /></x-input-span>
                                    <x-form-input name="dose" type="number" id="dose" placeholder="eg. 200"/>
                                    <x-input-span>Unit<x-required-span /></x-input-span>
                                    <x-select-unit aria-label="unit" name="unit" id="unit"></x-select-unit>
                                </x-form-div>
                                <x-form-div class="col-xl-4">
                                    <x-input-span>Frequency<x-required-span /></x-input-span>
                                    <x-select-frequency aria-label="frequency" name="frequency" id="frequency"></x-select-frequency>
                                    <x-input-span>Day(s)<x-required-span /></x-input-span>
                                    <x-form-input type="number" name="days" id="days" value="1" />
                                </x-form-div>
                                <x-form-div class="col-xl-4">
                                    <x-input-span>Set Time</x-input-span>
                                    <x-form-input type="datetime-local" name="date" id="date" value="" />
                                </x-form-div>
                                <div class="d-flex justify-content-center mt-2">
                                    <button type="button" id="saveMedicationChartBtn" data-btn="chart"
                                        class="btn btn-primary">
                                        Chart
                                    </button>
                                </div>
                                <x-toast-successful  id="saveMedicationChartToast"></x-toast-successful>
                            </div>
                        </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
