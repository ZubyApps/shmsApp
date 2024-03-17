<x-form-span>Bio</x-form-span>
<div class="row">
    <x-form-div class="">
        <x-input-span id="patientLabel">Patient</x-input-span>
        <x-form-input name="patientId" readonly value="" />
    </x-form-div>
    <x-form-div class="sponsorNameDiv">
        <x-input-span id="sponsorNameLabel">Sponsor Name</x-input-span>
        <x-form-input type="search" class="sponsorName" name="sponsorName" value="" readonly />
    </x-form-div>
    <x-form-div class="">
        <x-input-span>Age</x-input-span>
        <x-form-input name="age" class="age" value="" readonly />
    </x-form-div>
    <x-form-div class="">
        <x-input-span>Sex</x-input-span>
        <x-form-input name="sex" class="" value="" readonly />
    </x-form-div>
    <x-form-div>
        <x-input-span id="maritalStatusLabel">Marital Status</x-input-span>
        <x-form-input name="maritalStatus"  value="" readonly />
    </x-form-div>
    <x-form-div>
        <x-input-span>Phone Number</x-input-span>
        <x-form-input type="tel" name="phone" id="phone" value="" readonly />
    </x-form-div>
    <x-form-div>
        <x-input-span id="addressLabel">Address</x-input-span>
        <x-form-input name="address" value="" readonly />
    </x-form-div>
    <x-form-div>
        <x-input-span>Ethnic Group</x-input-span>
        <x-form-input name="ethnicGroup" value="" readonly />
    </x-form-div>
    <x-form-div>
        <x-input-span>Religion</x-input-span>
        <x-form-input name="religion" value="" readonly />
    </x-form-div>
    <x-form-div class="staffIdDiv">
        <x-input-span>Staff ID/No.</x-input-span>
        <x-form-input name="staffId" class="staffId" readonly />
    </x-form-div>
    <x-form-div>
        <x-input-span>Blood Group</x-input-span>
        <x-form-input name="bloodGroup" class="bloodGroup" readonly />
    </x-form-div>
    <x-form-div>
        <x-input-span>Genotype</x-input-span>
        <x-form-input name="genotype" class="genotype" readonly />
    </x-form-div>
    <x-form-div class="col-xl-12">
        <x-input-span>Known <br> Conditions</x-input-span>
        <x-form-textarea name="knownConditions" class="knownConditions text-danger fw-bold" readonly ></x-form-textarea>
    </x-form-div>
    
</div>
