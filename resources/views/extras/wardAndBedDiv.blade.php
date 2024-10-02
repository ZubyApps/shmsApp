<div class="row my-2">
    <x-form-div class="col-xl-6">
        <x-input-span id="admitLabel">Admit?<x-required-span /></x-input-span>
        <x-select-admit name="admit" id="admit" :disabled="false"></x-select-admit>
    </x-form-div>
    <x-form-div class="col-xl-6">
        <x-input-span id="wardLabel">Ward</x-input-span>
        <select class="form-select form-select-md" name="ward" id="ward">
            <option value="">Select Ward</option>
        </select>
        {{-- <x-select-ward name="ward"></x-select-ward> --}}
    </x-form-div>
    {{-- <x-form-div class="col-xl-4">
        <x-input-span id="bedNumberLabel">Bed Number</x-input-span>
        <x-select-bed name="bedNumber"></x-select-bed>
    </x-form-div> --}}
</div>