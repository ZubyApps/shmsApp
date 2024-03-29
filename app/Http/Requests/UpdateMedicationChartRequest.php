<?php

namespace App\Http\Requests;

use App\Models\MedicationChart;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMedicationChartRequest extends FormRequest
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
            'notGiven'  => ['required_if:doseGiven,null', 'prohibits:doseGiven'],
            'doseGiven' => ['required_if:notGiven,null', 'prohibits:notGiven'],
            'unit'      => ['required_with:doseGiven'],
            'note'      => ['required']
        ];
    }
}
