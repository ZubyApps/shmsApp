<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdmissionStatusRequest extends FormRequest
{
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
            'ward'              => ['required_if:admit,Inpatient,admit,Observation,', 'prohibited_if:admit,Outpatient'],
            // 'bedNumber'         => ['required_if:admit,=,Inpatient,admit,=,Observation'],
        ];
    }
}
