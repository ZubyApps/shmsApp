<?php

namespace App\Http\Requests;

use App\Models\Consultation;
use App\Models\Prescription;
use App\Models\Visit;
use Illuminate\Foundation\Http\FormRequest;

class StoreNursingChartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->designation?->designation == 'Nurse' || $this->user()->designation?->access_level > 3;
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
            'service'           => ['required'],
            'frequency'         => ['required'],
            'value'             => ['required'],
            'intervals'         => ['required'],
        ];
    }
}
