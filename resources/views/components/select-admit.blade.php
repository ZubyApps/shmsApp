@props(['disabled' => false])

<select {{ $attributes->merge(['class' => 'form-select form-select-md']) }}>
    <option value="">Select Admission Status</option>
    <option value="Outpatient">No</option>
    <option value="Inpatient" {{ $disabled  ? 'disabled' : '' }}>Yes</option>
    <option value="Observation">Observation</option>
</select>
