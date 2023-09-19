@props(['disabled' => false])

<select {{ $disabled  ? 'disabled' : '' }} {{ $attributes->merge(['class' => 'form-select form-select-md']) }}>
    <option value="">Select</option>
    <option value="A+">A+</option>
    <option value="A-">A-</option>
    <option value="B+">B+</option>
    <option value="B-">B-</option>
    <option value="AB+">AB+</option>
    <option value="AB-">AB-</option>
    <option selected value="O+">O+</option>
    <option value="O-">O-</option>
</select>
