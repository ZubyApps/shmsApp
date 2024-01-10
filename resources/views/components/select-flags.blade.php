<select {{ $attributes->merge(['class' => 'form-select form-select-md']) }}>
    <option value="">Select Sponsor to flag</option>
    <option value="None">None</option>
    <option value="NHIS">NHIS</option>
    <option value="HMO">HMO</option>
    <option value="Retainership">Retainership</option>
    <option value="NHIS/HMO">NHIS/HMO</option>
    <option value="NHIS/HMO/Retainership">NHIS/HMO/Retainership</option>
</select>