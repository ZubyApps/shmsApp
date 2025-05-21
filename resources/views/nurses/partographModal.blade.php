@vite(['resources/js/deliveryNoteMasks.js'])
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
                                    <x-form-input id="patient" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6">
                                    <x-input-span>Sponsor</x-input-span>
                                    <x-form-input id="sponsor" readonly/>
                                </x-form-div>
                                <x-form-div class="col-xl-6 d-none">
                                    <x-input-span>LBId</x-input-span>
                                    <x-form-input id="labourRecordId" readonly/>
                                </x-form-div>
                            </div>
                        </div>
                        <div class="mb-2 form-control cervixDescentDiv">
                            <div class="form-control mb-2">
                                <x-form-span>Fetal Heart Rate</x-form-span>
                                <div class="accordion " id="accordionFlushExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                            <span>Fetal Heart Rate Input & Table</span>
                                        </button>
                                        </h2>
                                        <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="fetalHeartRateTable" data-parametertype="fetal_heart_rate">
                                        <div class="accordion-body">
                                            <div class="form-control mb-1">
                                                    <x-form-span><small>Fetal Heart Rate Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span id="cervixDilationLabel">Fetal Heart Rate<x-required-span /></x-input-span>
                                                            <x-form-input name="bpm" class="value" id="fetal_heart_rate" type="number" />
                                                            <x-input-span>bpm</x-input-span>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="fetal_heart_rate" data-table="fetalHeartRateTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                            <div class="form-control overflow-auto my-3">
                                                <x-form-span><small>Fetal Heart Rate Table</small></x-form-span>
                                                <table id="fetalHeartRateTable" class="table table-hover align-middle table-sm fetatlHearRateTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Recorded At</th>
                                                            <th>Value</th>
                                                            <th>Recorded By</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-auto">
                                    <div class="chart-container" style="position: relative; height:80vh; width:70vw">
                                        <canvas id="fetalHeartRateChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control mb-2">
                                <x-form-span>Cervix Dialation & Presenting Part Descent</x-form-span>
                                <div class="accordion " id="accordionFlushExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                            <span>Cervical Dilation Input & Table</span>
                                        </button>
                                        </h2>
                                        <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="cervicalDilationTable" data-parametertype="cervical_dilation">
                                        <div class="accordion-body">
                                            <div class="form-control mb-1">
                                                    <x-form-span><small>Cervical Dilation</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span id="cervixDilationLabel">Cervical  Dilation<x-required-span /></x-input-span>
                                                            <x-form-input name="cm" class="value" id="cervical_dilation" type="number" />
                                                            <x-input-span>cm</x-input-span>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="cervical_dilation" data-table="cervicalDilationTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                            <div class="form-control overflow-auto my-3">
                                                <x-form-span><small>Cervical Dilation Table</small></x-form-span>
                                                <table id="cervicalDilationTable" class="table table-hover align-middle table-sm cervicalDilationTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Recorded At</th>
                                                            <th>Value</th>
                                                            <th>Recorded By</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                            <span>Descent Input & Table</span>
                                        </button>
                                        </h2>
                                        <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="descentTable" data-parameterType="descent">
                                        <div class="accordion-body">
                                            <div class="form-control">
                                                <x-form-span><small>Descent</small></x-form-span>
                                                <div class="row">
                                                    <x-form-div class="col-xl-6">
                                                        <x-input-span id="descentLabel">Descent<x-required-span /></x-input-span>
                                                        <x-form-input name="fifths" id="descent" class="value"/>
                                                    </x-form-div>
                                                    <x-form-div class="col-xl-6">
                                                        <x-input-span>Recorded At</x-input-span>
                                                        <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                    </x-form-div>
                                                </div>
                                                <div class="d-flex justify-content-center my-2">
                                                    <button type="button" id="addValueBtn"  data-param="descent" data-table="descentTable" class="btn btn-primary addValueBtn">
                                                        <i class="bi bi-plus-circle me-1"></i>
                                                        add
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="form-control overflow-auto my-3">
                                                <x-form-span><small>Presenting Part Descent Table</small></x-form-span>
                                                <table id="descentTable" class="table table-hover align-middle table-sm descentTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Recorded At</th>
                                                            <th>Value</th>
                                                            <th>Recorded By</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-auto">
                                    <div class="chart-container" style="position: relative; height:80vh; width:70vw">
                                        <canvas id="cervicalDescentChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control mb-2">
                                <x-form-span>Uterine Contractions</x-form-span>
                                <div class="accordion " id="accordionFlushExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                                            <span>Contractions Input & Table</span>
                                        </button>
                                        </h2>
                                        <div id="flush-collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="uterineContractionsTable" data-parametertype="uterine_contractions">
                                        <div class="accordion-body">
                                            <div class="form-control mb-1">
                                                <x-form-span><small>Uterine Contractions Input</small></x-form-span>
                                                <div class="row">
                                                    <x-form-div class="col-xl-6">
                                                        <x-input-span id="uterineContractionsCp10minLabel">Counts/10mins<x-required-span /></x-input-span>
                                                        <x-form-input class="value" name="count_per_10min" id="uterineContractionsCp10min" type="number" />
                                                    </x-form-div>
                                                    <x-form-div class="col-xl-6">
                                                        <x-input-span id="uterineContractionsLsLabel">Lasting<x-required-span /></x-input-span>
                                                        <x-form-input class="value" name="lasting_seconds" id="uterineContractionsLs" type="number" />
                                                        <x-input-span>seconds</x-input-span>
                                                    </x-form-div>
                                                    <x-form-div class="col-xl-6">
                                                        <x-input-span id="uterineContractionsStrengthLabel">Strength<x-required-span /></x-input-span>
                                                        <select class="form-select form-select-md value" name="strength" id="uterineContractionsStrength">
                                                            <option value="">Select</option>
                                                            <option value="Weak">Weak</option>
                                                            <option value="Moderate">Moderate</option>   
                                                            <option value="Strong">Strong</option>   
                                                        </select>
                                                    </x-form-div>
                                                    <x-form-div class="col-xl-6">
                                                        <x-input-span>Recorded At</x-input-span>
                                                        <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                    </x-form-div>
                                                </div>
                                                <div class="d-flex justify-content-center my-2">
                                                    <button type="button" id="addValueBtn" data-param="uterine_contractions" data-table="uterineContractionsTable" class="btn btn-primary addValueBtn">
                                                        <i class="bi bi-plus-circle me-1"></i>
                                                        add
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="form-control overflow-auto my-3">
                                                <x-form-span><small>Uterine Contractions Table</small></x-form-span>
                                                <table id="uterineContractionsTable" class="table table-hover align-middle table-sm uterineContractionsTable">
                                                    <thead>
                                                        <tr>
                                                            <th>Recorded At</th>
                                                            <th>Value</th>
                                                            <th>Recorded By</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody></tbody>
                                                </table>
                                            </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-auto">
                                    <div class="chart-container" style="position: relative; height:80vh; width:70vw">
                                        <canvas id="uterineContractionsChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control mb-2">
                                <x-form-span>Blood Pressure & Pulse</x-form-span>
                                <div class="accordion" id="accordionFlushExample">
                                    <!-- Blood Pressure -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseBloodPressure" aria-expanded="false" aria-controls="flush-collapseBloodPressure">
                                                <span>Blood Pressure Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapseBloodPressure" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="bloodPressureTable" data-parametertype="blood_pressure">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Blood Pressure Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Systolic<x-required-span /></x-input-span>
                                                            <x-form-input class="value" name="systolic" id="bloodPressureSystolic" type="number" />
                                                            <x-input-span>mmHg</x-input-span>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Diastolic<x-required-span /></x-input-span>
                                                            <x-form-input class="value" name="diastolic" id="bloodPressureDiastolic" type="number" />
                                                            <x-input-span>mmHg</x-input-span>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="blood_pressure" data-table="bloodPressureTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Blood Pressure Table</small></x-form-span>
                                                    <table id="bloodPressureTable" class="table table-hover align-middle table-sm bloodPressureTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Values</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Pulse -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapsePulse" aria-expanded="false" aria-controls="flush-collapsePulse">
                                                <span>Pulse Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapsePulse" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="pulseTable" data-parametertype="pulse">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Pulse Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Pulse<x-required-span /></x-input-span>
                                                            <x-form-input class="value" name="bpm" id="pulseBpm" type="number" />
                                                            <x-input-span>bpm</x-input-span>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="pulse" data-table="pulseTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Pulse Table</small></x-form-span>
                                                    <table id="pulseTable" class="table table-hover align-middle table-sm pulseTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Pulse (bpm)</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-auto">
                                    <div class="chart-container" style="position: relative; height:80vh; width:70vw">
                                        <canvas id="bloodPressurePulseChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control mb-2">
                                <x-form-span>Temperature</x-form-span>
                                <div class="accordion" id="accordionFlushExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTemperature" aria-expanded="false" aria-controls="flush-collapseTemperature">
                                                <span>Temperature Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapseTemperature" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="temperatureTable" data-parametertype="temperature">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Temperature Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Temperature<x-required-span /></x-input-span>
                                                            <x-form-input class="value" name="celsius" id="temperature"  step="0.1" />
                                                            <x-input-span>°C</x-input-span>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="temperature" data-table="temperatureTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Temperature Table</small></x-form-span>
                                                    <table id="temperatureTable" class="table table-hover align-middle table-sm temperatureTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Temperature (°C)</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-auto">
                                    <div class="chart-container" style="position: relative; height:80vh; width:70vw">
                                        <canvas id="temperatureChart"></canvas>
                                    </div>
                                </div>
                            </div>
                            <div class="form-control mb-2">
                                <x-form-span>Maternal and Fetal Observations</x-form-span>
                                <div class="accordion" id="accordionFlushExample">
                                    <!-- Urine -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseUrine" aria-expanded="false" aria-controls="flush-collapseUrine">
                                                <span>Urine Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapseUrine" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="urineTable" data-parametertype="urine">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Urine Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Protein<x-required-span /></x-input-span>
                                                            <select class="form-select form-select-md value" name="protein" id="urineProtein">
                                                                <option value="">Select</option>
                                                                <option value="Negative">Negative</option>
                                                                <option value="+">+</option>
                                                                <option value="++">++</option>
                                                                <option value="+++">+++</option>
                                                            </select>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Glucose<x-required-span /></x-input-span>
                                                            <select class="form-select form-select-md value" name="glucose" id="urineGlucose">
                                                                <option value="">Select</option>
                                                                <option value="Negative">Negative</option>
                                                                <option value="+">+</option>
                                                                <option value="++">++</option>
                                                                <option value="+++">+++</option>
                                                            </select>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Volume</x-input-span>
                                                            <x-form-input class="value" name="volume" id="urineVolume" type="number" />
                                                            <x-input-span>mL</x-input-span>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="urine" data-table="urineTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Urine Table</small></x-form-span>
                                                    <table id="urineTable" class="table table-hover align-middle table-sm urineTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Value</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Caput -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseCaput" aria-expanded="false" aria-controls="flush-collapseCaput">
                                                <span>Caput Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapseCaput" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="caputTable" data-parametertype="caput">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Caput Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Degree<x-required-span /></x-input-span>
                                                            <select class="form-select form-select-md value" name="degree" id="caputDegree">
                                                                <option value="">Select</option>
                                                                <option value="0">0</option>
                                                                <option value="+">+</option>
                                                                <option value="++">++</option>
                                                                <option value="+++">+++</option>
                                                            </select>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="caput" data-table="caputTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Caput Table</small></x-form-span>
                                                    <table id="caputTable" class="table table-hover align-middle table-sm caputTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Degree</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Position -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapsePosition" aria-expanded="false" aria-controls="flush-collapsePosition">
                                                <span>Position Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapsePosition" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="positionTable" data-parametertype="position">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Position Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Position<x-required-span /></x-input-span>
                                                            <select class="form-select form-select-md value" name="position" id="positionValue">
                                                                <option value="">Select</option>
                                                                <option value="OA">OA</option>
                                                                <option value="OP">OP</option>
                                                                <option value="LOA">LOA</option>
                                                                <option value="ROA">ROA</option>
                                                                <option value="LOT">LOT</option>
                                                                <option value="ROT">ROT</option>
                                                            </select>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="position" data-table="positionTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Position Table</small></x-form-span>
                                                    <table id="positionTable" class="table table-hover align-middle table-sm positionTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Position</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Moulding -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseMoulding" aria-expanded="false" aria-controls="flush-collapseMoulding">
                                                <span>Moulding Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapseMoulding" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="mouldingTable" data-parametertype="moulding">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Moulding Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Degree<x-required-span /></x-input-span>
                                                            <select class="form-select form-select-md value" name="degree" id="mouldingDegree">
                                                                <option value="">Select</option>
                                                                <option value="0">0</option>
                                                                <option value="+">+</option>
                                                                <option value="++">++</option>
                                                                <option value="+++">+++</option>
                                                            </select>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="moulding" data-table="mouldingTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Moulding Table</small></x-form-span>
                                                    <table id="mouldingTable" class="table table-hover align-middle table-sm mouldingTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Degree</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Oxytocin -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOxytocin" aria-expanded="false" aria-controls="flush-collapseOxytocin">
                                                <span>Oxytocin Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapseOxytocin" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="oxytocinTable" data-parametertype="oxytocin">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Oxytocin Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Dosage<x-required-span /></x-input-span>
                                                            <x-form-input class="value" name="dosage" id="oxytocinDosage" type="number" />
                                                            <x-input-span>units</x-input-span>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="oxytocin" data-table="oxytocinTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Oxytocin Table</small></x-form-span>
                                                    <table id="oxytocinTable" class="table table-hover align-middle table-sm oxytocinTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Dosage</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Fluid -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseFluid" aria-expanded="false" aria-controls="flush-collapseFluid">
                                                <span>Fluid Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapseFluid" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="fluidTable" data-parametertype="fluid">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Fluid Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Status<x-required-span /></x-input-span>
                                                            <select class="form-select form-select-md value" name="status" id="fluidStatus">
                                                                <option value="">Select</option>
                                                                <option value="Clear">Clear</option>
                                                                <option value="Meconium">Meconium-stained</option>
                                                                <option value="Bloody">Bloody</option>
                                                            </select>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="fluid" data-table="fluidTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Fluid Table</small></x-form-span>
                                                    <table id="fluidTable" class="table table-hover align-middle table-sm fluidTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Status</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Drug -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseDrug" aria-expanded="false" aria-controls="flush-collapseDrug">
                                                <span>Drug Input & Table</span>
                                            </button>
                                        </h2>
                                        <div id="flush-collapseDrug" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample" data-table="drugTable" data-parametertype="drug">
                                            <div class="accordion-body">
                                                <div class="form-control mb-1">
                                                    <x-form-span><small>Drug Input</small></x-form-span>
                                                    <div class="row">
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Type<x-required-span /></x-input-span>
                                                            <select class="form-select form-select-md value" name="type" id="drugType">
                                                                <option value="">Select</option>
                                                                <option value="Penicillin">Penicillin</option>
                                                                <option value="Analgesic">Analgesic</option>
                                                                <option value="Other">Other</option>
                                                            </select>
                                                        </x-form-div>
                                                        <x-form-div class="col-xl-6">
                                                            <x-input-span>Recorded At</x-input-span>
                                                            <x-form-input type="datetime-local" name="recordedAt" id="recordedAt"/>
                                                        </x-form-div>
                                                    </div>
                                                    <div class="d-flex justify-content-center my-2">
                                                        <button type="button" id="addValueBtn" data-param="drug" data-table="drugTable" class="btn btn-primary addValueBtn">
                                                            <i class="bi bi-plus-circle me-1"></i>
                                                            add
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="form-control overflow-auto my-3">
                                                    <x-form-span><small>Drug Table</small></x-form-span>
                                                    <table id="drugTable" class="table table-hover align-middle table-sm drugTable">
                                                        <thead>
                                                            <tr>
                                                                <th>Recorded At</th>
                                                                <th>Type</th>
                                                                <th>Recorded By</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody></tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="overflow-auto mt-2">
                                    <div class="chart-container" style="position: relative; height:80vh; width:70vw">
                                        <canvas id="observationsChart"></canvas>
                                    </div>
                                </div>
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
