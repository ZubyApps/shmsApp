    <x-form-div>
        <x-input-span>Blood Group<x-required-span /></x-input-span>
        <x-select-bloodgroup name="bloodGroup" :disabled="$disabled" :value="old('bloodGroup')" />
    </x-form-div>
    <x-input-error :messages="$errors->get('bloodGroup')" class="mt-1" />

    <x-form-div>
        <x-input-span>Genotype</x-input-span>
        <select class="form-select form-select-md" aria-label="genotype" name="genotype" {{ $disabled ? 'disabled' : '' }} :value="old('genotype')">
            <option value="">Select</option>
            <option value="AA">AA</option>
            <option value="AS">AS</option>
            <option value="SS">SS</option>
        </select>
    </x-form-div>
    <x-input-error :messages="$errors->get('bloodGroup')" class="mt-1" />

    <x-form-div>
        <x-input-span>Known <br> Conditions</x-input-span>
        <x-form-textarea name="knownConditions" class="knownConditions" cols="auto" rows="auto"
            placeholder="eg: Diabetic, Hypertension, Atopy" value="Hypertensive" :disabled="$disabled" :value="old('knownConditions')">Hypertensive <br> Diabetic</x-form-textarea>
    </x-form-div>
    <x-input-error :messages="$errors->get('knownConditions')" class="mt-1" />
