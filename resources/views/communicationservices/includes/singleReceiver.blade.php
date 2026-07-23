<div>
    <x-form-div class="col-xl-12">
        <x-input-span>Source</x-input-span>
        <select class="form-select form-select-md" name="singleSource" id="singleSource">
            <option value="">Select</option>
            <option value="patient">Patient</option>
            <option value="staff">Staff</option>
            <option value="number">Phone Number</option>
        </select>
    </x-form-div>
    <div class="mb-2 form-control singleSourceDiv d-none">
        <x-form-div class="col-xl-12 searchPatientDiv d-none">
            <x-input-span id="patientLabel">Search Patient<x-required-span /></x-input-span>
            <input class="form-control item" type="search" name="patient" id="patient" autocomplete="off" placeholder="search patient" list="patientList"/>
            <datalist name="patient" type="text" class="decoration-none patientList" id="patientList"></datalist>
        </x-form-div>
        <x-form-div class="col-xl-12 searchStaffDiv d-none">
            <x-input-span id="staffLabel">Search Staff<x-required-span /></x-input-span>
            <input class="form-control item" type="search" name="staff" id="staff" autocomplete="off" placeholder="search staff" list="staffList"/>
            <datalist name="staff" type="text" class="decoration-none staffList" id="staffList"></datalist>
        </x-form-div>
        <x-form-div class="col-xl-12 typeNumberDiv d-none">
            <x-input-span>Phone Number</x-input-span>
            <x-form-input name="phone" id="phone"/>
        </x-form-div>
    </div>
</div>