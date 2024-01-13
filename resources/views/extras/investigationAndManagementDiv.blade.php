<div class="d-none investigationAndManagementDiv" id="investigationAndManagementDiv{{ $type}}" data-div="{{ strtolower($type) }}">
    <div class="mb-2 form-control addDiv">
        <x-form-span>Investigation & Management</x-form-span>
        <div class="row">
            <x-form-div class="col-xl-6">
                <x-input-span id="resourceLabel">Medical Resource<x-required-span /></x-input-span>
                <input class="form-control resource" type="search" name="resource" id="resource" data-input="{{ strtolower($type) }}" placeholder="search" list="resourceList{{ strtolower($type) }}"/>
                <datalist name="resource" type="text" class="decoration-none resourceList" id="resourceList{{ strtolower($type) }}"></datalist>
            </x-form-div>
            <x-form-div class="col-xl-6 pres" id="pres">
                <x-input-span id="prescriptionLabel">Prescription<x-required-span /></x-input-span>
                <x-form-input type="text" name="prescription" id="prescription"
                    placeholder="eg: 5mg BD x5/7" />
            </x-form-div>
            <x-form-div class="col-xl-6 qty" id="qty">
                <x-input-span id="quantityLabel">Quantity<x-required-span /></x-input-span>
                <x-form-input type="number" name="quantity" id="quantity"
                    placeholder="" value="1"/>
            </x-form-div>
            <x-form-div class="col-xl-6">
                <x-input-span id="noteLabel">Note</x-input-span>
                <x-form-input type="text" name="note" id="note"/>
            </x-form-div>
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
                    <th>Qty</th>
                    <th>Note</th>
                    <th>By</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>