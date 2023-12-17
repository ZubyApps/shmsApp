<?php

namespace App\Http\Requests;

use App\Models\Visit;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreConsultationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'visitId'              => ['required', 'numeric', 'exists:'.Visit::class.',id'],
            // 'selectedDiagnosis'    => ['required'],
            'provisionalDiagnosis' => ['required_if:selectedDiagnosis,null'],
            'admit'                => ['required'],
            // 'ward'                 => ['required_if:admit,Inpatient,admit,Observation,'],
            // 'bedNumber'            => ['required_if:admit,=,Inpatient,admit,=,Observation'],
        ];
    }
}
