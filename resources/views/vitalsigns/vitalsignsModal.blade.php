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
                                    <x-form-input name="sponsor" value="" id="sponsor"/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control vitalsDiv">
                            <x-form-span>Vital Signs</x-form-span>
                            <div class="row overflow-auto my-3">
                                <table id="vitalSignsTable" class="table table-hover align-middle table-sm vitalsTable">
                                    <thead>
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
                                            <th>By</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                            <div class="row {{ $isDoctor ? 'd-none' : '' }}">
                                <div class="row" id="addVitalsignsDiv"  data-div="waiting">
                                    @include('vitalsigns.vitalsigns', ['sf' => 'nurses'])
                                    <x-toast-successful class="col-xl-12"  id="vitalSignsToast"></x-toast-successful>
                                </div>
                                <div class="d-flex justify-content-center">
                                    <button type="button" id="addVitalsignsBtn"  data-btn="waiting"
                                        class="btn btn-primary">
                                        <i class="bi bi-plus-circle me-1"></i>
                                        add
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-auto {{ !$isDoctor ? 'd-none' : '' }}">
                            <div class="chart-container" style="position: relative; height:60vh; width:80vw">
                                <canvas id="vitalsignsChart"></canvas>
                            </div>
                        </div>
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
