<select {{ $attributes->merge(['class' => 'form-select form-select-md']) }}>
    <option value="">Select Reason</option>
    <option value="Patient Declined">Patient Declined</option>
    <option value="Doctor's Order">Doctor's Order</option>
    <option value="Patient Abscent">Patient Abscent</option>
    <option value="Physical Barrier">Physical Barrier</option>
    <option value="Adverse Reaction">Adverse Reaction</option>
    <option value="Other">Other(explain in notes)</option>
</select>
