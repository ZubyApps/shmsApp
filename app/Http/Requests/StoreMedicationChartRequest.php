<?php

namespace App\Http\Requests;

use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\Visit;
use Illuminate\Foundation\Http\FormRequest;

class StoreMedicationChartRequest extends FormRequest
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
            'prescriptionId'    => ['required', 'integer', 'exists:'.Prescription::class.',id'],
            'conId'             => ['required', 'integer', 'exists:'.Consultation::class.',id'],
            'visitId'           => ['required', 'integer', 'exists:'.Visit::class.',id'],
            'dose'              => ['required', 'integer', 'min:1'],
            'days'              => ['required', 'integer', 'min:1'],
            'unit'              => ['required'],
            'frequency'         => ['required']
        ];
    }
}
