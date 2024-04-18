<select {{ $attributes->merge(['class' => 'form-select form-select-md']) }}>
    <option value="">Select Reason</option>
    <option value="Patient No Show">Patient No Show (for OutPatients)</option>
    <option value="Patient Abscent">Patient Abscent (for Inpatients)</option>
    <option value="Patient Declined">Patient Declined</option>
    <option value="Not Paid">Not Paid</option>
    <option value="Doctor's Order">Doctor's Order</option>
    <option value="Physical Barrier">Physical Barrier</option>
    <option value="Not Equipped">Not Equipped</option>
    <option value="Other">Other</option>
</select>
