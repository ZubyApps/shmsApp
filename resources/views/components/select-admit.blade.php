@props(['disabled' => false])

<select {{ $attributes->merge(['class' => 'form-select form-select-md']) }} {{ $disabled  ? 'disabled' : '' }}>
    <option value="">Select Admission Status</option>
    <option value="Outpatient">No</option>
    <option value="Inpatient">Yes</option>
    <option value="Observation">Observation</option>
</select>
