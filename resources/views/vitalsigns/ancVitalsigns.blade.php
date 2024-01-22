@vite(['resources/js/vitalSignsMasks.js'])
<div class="row" id="{{ $sf }}">
    <x-form-div>
        <x-input-span id="bloodPressureLabel">Blood Pressure</x-input-span>
        <x-form-input type="text" name="bloodPressure" id="bloodPressure" />
    </x-form-div>
    <x-form-div>
        <x-input-span id="urineProteinLabel">Urine-Protein</x-input-span>
        <x-form-input type="text" name="urineProtein" id="urineProtein" />
    </x-form-div>
    <x-form-div>
        <x-input-span id="urineGlucoseLabel">Urine Glucose</x-input-span>
        <x-form-input type="text" name="urineGlucose" id="urineGlucose"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="weightLabel">Weight</x-input-span>
        <x-form-input type="text" name="weight" id="weight" data-id="{{ $sf }}"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="remarksLabel">Remarks</x-input-span>
        <x-form-input type="text" name="remarks" id="remarks" class="remarks"/>
    </x-form-div>
</div>
