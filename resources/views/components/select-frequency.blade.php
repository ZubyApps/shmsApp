<select {{ $attributes->merge(['class' => 'form-select form-select-md']) }}>
        <option value="">Select</option>
        <optgroup label="Terms" id="terms">
                <option value="24">stat</option>
                <option value="24.0">Daily</option>
                <option value="12">BD</option>
                <option value="8">TDS</option>
                <option value="6">QDS</option>
                <option value="168">Weekly</option>  
                <option value="672">Monthly</option>
        </optgroup>
        <optgroup label="In Hours" id="hours">
                <option value="24">24hrly</option>
                <option value="12">12hrly</option>
                <option value="8">8hrly</option>
                <option value="6">6hrly</option>
                <option value="4">4hrly</option>
                <option value="2">2hrly</option>
                <option value="1">1hrly</option>
        </optgroup>
        <optgroup label="In Minutes" id="minutes">
                <option value="10">10minly</option>  
                <option value="30">30minly</option>  
                <option value="60">60minly</option>  
        </optgroup> 
</select>