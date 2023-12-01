<?php

namespace App\Http\Requests;

use App\Models\Prescription;
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
            'prescriptionId'    => ['required', 'numeric', 'exists:'.Prescription::class.',id'],
            'conId'             => ['required', 'numeric', 'exists:'.Prescription::class.',id'],
            'visitId'           => ['required', 'numeric', 'exists:'.Prescription::class.',id'],
            'dose'              => ['required'],
            'days'              => ['required', 'numeric', 'min:1'],
            'unit'              => ['required'],
            'frequency'         => ['required']
        ];
    }
}
