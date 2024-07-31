<div class="d-none investigationAndManagementDiv" id="investigationAndManagementDiv{{ $type}}" data-div="{{ strtolower($type) }}">
    <div class="mb-2 form-control addDiv">
        <x-form-span>Investigation & Management</x-form-span>
        <div class="row">
            <x-form-div class="col-xl-6">
                <x-input-span id="resourceLabel">Medical Resource<x-required-span /></x-input-span>
                <input class="form-control resource" type="search" autocomplete="off" name="resource" id="resource" data-input="{{ strtolower($type) }}" placeholder="search" list="resourceList{{ strtolower($type) }}"/>
                <datalist name="resource" type="text" class="decoration-none resourceList" id="resourceList{{ strtolower($type) }}"></datalist>
            </x-form-div>
            <x-form-div class="col-xl-6 pres" id="pres">
                <x-input-span class="px-1">Dose</x-input-span>
                <x-form-input name="dose" type="number" id="dose" placeholder="eg. 200"/>
                <x-input-span class="px-1">Unit</x-input-span>
                <x-select-unit aria-label="unit" name="unit" id="unit"></x-select-unit>
                <x-input-span class="px-1">Freq</x-input-span>
                <x-select-frequency-text aria-label="frequency" name="frequency" id="frequency" :hidden="$isNurse"></x-select-frequency>
                <x-input-span class="px-1">Day(s)</x-input-span>
                <x-form-input type="number" name="days" id="days" value="1" />
            </x-form-div>
            <x-form-div class="col-xl-6 qty" id="qty">
                <x-input-span id="quantityLabel">Quantity<x-required-span /></x-input-span>
                <x-form-input type="number" name="quantity" id="quantity" placeholder="" value="1"/>
            </x-form-div>
            @if ($isNurse)
                <x-form-div class="col-xl-6">
                    <x-input-span id="docLabel">DOC</x-input-span>
                    <select type="text" name="doc" id="doc" class="form-select form-select-md">
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" name="{{ $doctor->username }}">{{ $doctor->username }}</option>
                        @endforeach
                    </select>
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span id="noteLabel">Route</x-input-span>
                    <x-select-route aria-label="route" name="route" id="route"></x-select-route>
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span id="noteLabel">Note/Instruction</x-input-span>
                    <x-form-input type="text" name="note" id="note"/>
                </x-form-div>
            @else
                <x-form-div class="col-xl-6 chartableDiv d-none" id="chartableDiv">
                        <x-input-span id="chartableLabel" class="py-0">Chartable?</x-input-span>
                        <x-form-input class="form-check-input py-3 mt-0" type="checkbox" id="chartable" />
                </x-form-div>
                <x-form-div class="col-xl-6">
                    <x-input-span id="noteLabel">Route</x-input-span>
                    <x-select-route aria-label="route" name="route" id="route"></x-select-route>
                    <x-input-span id="noteLabel">Note/Instruction</x-input-span>
                    <x-form-input type="text" name="note" id="note"/>
                </x-form-div>
            @endif
        </div>
        <div class="d-flex justify-content-center">
            <button type="button" id="addInvestigationAndManagementBtn" data-btn="{{ strtolower($type) }}" class="btn btn-primary">
                add
                <i class="bi bi-prescription"></i>
            </button>
        </div>
        <x-toast-successful  id="saveInvestigationAndManagementToast"></x-toast-successful>
    </div>
    <div class="mb-2 form-control overflow-auto">
        <table id="prescriptionTable{{ $type }}" class="table table-hover align-middle table-sm prescriptionTable">
            <thead>
                <tr>
                    <th>Prescribed</th>
                    <th>Resource</th>
                    <th>Prescription</th>
                    <th>Route</th>
                    <th>Qty</th>
                    <th>Note</th>
                    <th>Chartable</th>
                    <th>By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>