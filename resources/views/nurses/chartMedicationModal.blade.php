<div class="modal fade" id="{{ $id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fs-4 text-primary">{{ $title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2">
                    <x-form-label>Chart Medication</x-form-label>
                    <div class="row">
                        <x-form-div>
                            <x-input-span>Patient</x-input-span>
                            <x-form-input name="patient" value="" id="patient"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Sponsor</x-input-span>
                            <x-form-input name="sponsor" value="" id="sponsor"/>
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Treatment<x-required-span /></x-input-span>
                            <x-form-input type="text" name="treatment" value="" id="resource" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Prescription</x-input-span>
                            <x-form-input name="prescription" value="" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Prescribed By<x-required-span /></x-input-span>
                            <x-form-input type="text" name="prescriptionBy" value="Dr Toby" />
                        </x-form-div>
                        <x-form-div>
                            <x-input-span>Prescribed</x-input-span>
                            <x-form-input type="datetime-local" name="prescribed" value="" id="prescribed" />
                        </x-form-div>
                        <X-form-div class="py-4">
                            <table id="medicationChart" class="table table-hover align-middle table-sm bg-primary">
                                <thead>
                                    <tr>
                                        <th>Medication</th>
                                        <th>Prescription</th>
                                        <th>Date/Time</th>
                                        <th>Charted By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Pentazocine Inj</td>
                                        <td>300mg 8hrly 2/7</td>
                                        <td>08/10/2023</td>
                                        <td>Nurse Aba</td>
                                        <td><button class="btn btn-outline-primary deleteBtn"><i class="bi bi-trash"></i></button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </X-form-div>
                        <div class="row" id="chartMedicationDiv">
                            <x-form-span class="mb-2 fw-semibold">Save the time intervals according to prescription</x-form-span>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Dose</x-input-span>
                                <x-form-input name="dose" value="300mg 8hrly 2/7" readonly />
                            </x-form-div>
                            <x-form-div class="col-xl-6">
                                <x-input-span>Date/Time</x-input-span>
                                <x-form-input type="datetime-local" name="dateTime" value="" />
                            </x-form-div>
                            <div class="d-flex justify-content-center mt-2">
                                <button type="button" id="saveMedicationChartBtn" data-btn="${iteration}"
                                    class="btn btn-primary">
                                    save
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>
                    Close
                </button>
                {{-- <button type="button" id="{{ $isUpdate ? 'saveBtn' : 'createBtn' }}" class="btn bg-primary text-white">
                    <i class="bi bi-check-circle me-1"></i>
                    {{ $isUpdate ? 'Update' : 'Create' }}
                </button> --}}
            </div>
        </div>
    </div>
</div>
