@vite(['resources/js/vitalSignsMasks.js'])
<div class="row" id="{{ $sf }}">
    <x-form-div>
        <x-input-span id="temperatureLabel">Temparature</x-input-span>
        <x-form-input name="temperature" id="temperature" data-maska="##.#°C"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="bloodPressureLabel">Blood Pressure</x-input-span>
        <x-form-input type="text" name="bloodPressure" id="bloodPressure" />
    </x-form-div>
    <x-form-div>
        <x-input-span id="pulseRateLabel">Pulse Rate</x-input-span>
        <x-form-input type="text" name="pulseRate" id="pulseRate" />
    </x-form-div>
    <x-form-div>
        <x-input-span id="respiratoryRateLabel">Respiratory Rate</x-input-span>
        <x-form-input type="text" name="respiratoryRate" id="respiratoryRate" data-maska="##cpm"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="spO2Label">SpO2</x-input-span>
        <x-form-input name="spO2" id="spO2" />
    </x-form-div>
    <x-form-div>
        <x-input-span id="weightLabel">Weight</x-input-span>
        <x-form-input type="text" name="weight" id="weight" class="weight" data-id="{{ $sf }}"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="heightLabel">Height/Length</x-input-span>
        <x-form-input type="text" name="height" id="height" class="height" data-id="{{ $sf }}"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="bmiLabel">BMI (Healthy range: 18.5-24.9)</x-input-span>
        <x-form-input type="text" name="bmi" id="bmi" value="" class="bmi"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="heightLabel">Head Circ</x-input-span>
        <x-form-input type="text" name="headCircumference" id="headCircumference" class="headCircumference" data-id="{{ $sf }}"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="heightLabel">Mid Arm Circ</x-input-span>
        <x-form-input type="text" name="midArmCircuference" id="midArmCircuference" class="midArmCircuference" data-id="{{ $sf }}"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="heightLabel">Fluid Drain</x-input-span>
        <x-form-input type="text" name="fluidDrain" id="fluidDrain" class="fluidDrain" data-id="{{ $sf }}"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="heightLabel">Urine Output</x-input-span>
        <x-form-input type="text" name="urineOutput" id="urineOutput" class="urineOutput" data-id="{{ $sf }}"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="heightLabel">Fetal HR</x-input-span>
        <x-form-input type="text" name="fetalHr" id="fetalHr" class="fetalHr" data-id="{{ $sf }}"/>
    </x-form-div>
    <x-form-div>
        <x-input-span id="noteLabel">Note</x-input-span>
        <x-form-textarea type="text" name="note" id="note" class="note"/>
    </x-form-div>
</div>
