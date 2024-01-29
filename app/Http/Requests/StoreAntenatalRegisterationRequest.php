<?php

namespace App\Http\Requests;

use App\Models\Patient;
use Illuminate\Foundation\Http\FormRequest;

class StoreAntenatalRegisterationRequest extends FormRequest
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
            'maritalStatus'         => ['required'],
            'lmp'                   => ['required'],
            'previousPregnancies'   => ['required'],
            'totalPregnancies'      => ['required'],
            'noOfLivingChildren'    => ['required'],
            // 'rvst'                  => ['required'],
            'patientId'             => ['required', 'numeric', 'exists:'.Patient::class.',id'],
        ];
    }
}
