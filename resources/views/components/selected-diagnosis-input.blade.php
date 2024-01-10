<x-form-div class="col-xl-6">
    <x-input-span id="diagnosisLabel">Selected <br />ICD11 Diagnosis<x-required-span /></x-input-span>
    <i class="bi bi-arrow-clockwise"></i>
    <x-form-textarea type="text" name="selectedDiagnosis" class="selectedDiagnosis-{{ $isSpecialist ? '3' : '1' }}" id="selectedDiagnosis" readonly></x-form-textarea>
</x-form-div>