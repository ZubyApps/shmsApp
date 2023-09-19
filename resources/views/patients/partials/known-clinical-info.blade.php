    <x-form-div>
        <x-input-span>Blood Group<x-required-span /></x-input-span>
        <x-select-bloodgroup name="bloodGroup" :disabled="$disabled"  />
    </x-form-div>
    <x-input-error :messages="$errors->get('bloodGroup')" class="mt-1" />

    <x-form-div>
        <x-input-span>Genotype</x-input-span>
        <select class="form-select form-select-md" aria-label="genotype" name="genotype" {{ $disabled ? 'disabled' : '' }} >
            <option value="">Select</option>
            <option selected value="AA">AA</option>
            <option value="AS">AS</option>
            <option value="SS">SS</option>
        </select>
    </x-form-div>
    <x-input-error :messages="$errors->get('bloodGroup')" class="mt-1" />

    <x-form-div>
        <x-input-span>Known <br> Conditions</x-input-span>
        <x-form-textarea name="knownConditions" class="knownConditions" cols="10" rows="2"
            placeholder="eg: Diabetic, Hypertension, Atopy" value="Hypertensive" :disabled="$disabled">Hypertensive <br> Diabetic</x-form-textarea>
    </x-form-div>
    <x-input-error :messages="$errors->get('knownConditions')" class="mt-1" />
