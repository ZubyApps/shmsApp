@props(['hidden' => false])
<select {{ $attributes->merge(['class' => 'form-select form-select-md']) }}>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="">Select</option>
        <option value="stat">stat</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="Daily">Daily</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="12hrly">12hrly</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="8hrly">8hrly</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="6hrly">6hrly</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="4hrly">4hrly</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="2hrly">2hrly</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="1hrly">1hrly</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="BD">BD</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="TDS">TDS</option>
        <option {{ $hidden  ? 'hidden disabled' : '' }} value="QDS">QDS</option>
</select>
