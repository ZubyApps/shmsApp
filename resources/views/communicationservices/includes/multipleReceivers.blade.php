<div>
    <x-form-div class="col-xl-12">
        <x-input-span>Group</x-input-span>
            <select class="form-select form-select-md" name="multiSource" id="multiSource">
                <option value="">Select</option>
                <option value="hmsPatients">HMS Patients</option>
                <option value="hmsStaff">HMS Staff</option>
                <option value="numbers">Phone Numbers</option>
            </select>
    </x-form-div>
    <div class="mb-2 form-control d-none multiSourceDiv">
        <div class="hmsPatientsDiv d-none">
            <x-form-div class="col-xl-12 categoryDiv">
                <x-input-span>Patient Category</x-input-span>
                <select class="form-select form-select-md" name="patientCategory" id="patientCategory">
                    <option value="">Select</option>
                    <option value="registered">Registered</option>
                    <option value="visited">Visited</option>
                </select>
            </x-form-div>
            <x-form-div class="col-xl-12 datesDiv">
                <x-input-span class="">Start</x-input-span>
                <x-form-input type="date" name="startDate" id="startDate" />
                <x-input-span class="">End</x-input-span>
                <x-form-input type="date" name="endDate" id="endDate" />
            </x-form-div>
        </div>
        <x-form-div class="col-xl-12 hmsStaffDiv d-none">
            <x-form-div class="col-xl-12">
                <x-input-span>Designation<x-required-span /></x-input-span>
                <select class="form-select form-select-md" name="designation" id="designation">
                    <option value="">Select</option>
                    <option value="All">All</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Nurse">Nurse</option>
                    <option value="Lab Tech">Lab Tech</option>
                    <option value="Pharmacy Tech">Pharmacy Tech</option>
                    <option value="Bill Officer">Bill Officer</option>
                    <option value="Records Clerk">Records Clerk</option>
                    <option value="HMO Officer">HMO Officer</option>
                    <option value="Admin">Admin</option>
                    <option value="IT Officer">IT Officer</option>
                    <option value="Maid">Maid</option>
                    <option value="Security">Security</option>
                </select>
            </x-form-div>
        </x-form-div>
        <x-form-div class="col-xl-12 numbersDiv d-none">
            <x-input-span>Phone Numbers</x-input-span>
            <x-form-textarea name="phones" id="phones" placeholder="separate with commas"/>
        </x-form-div>
    </div>

</div>