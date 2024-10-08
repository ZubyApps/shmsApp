<?php

namespace App\Http\Requests;

use App\Models\Patient;
use App\Models\Sponsor;
use Illuminate\Foundation\Http\FormRequest;

class GenerateFormRequest extends FormRequest
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
            "patientType"       => ['required'],
            "sponsorCategory"   => ['required'],
            "cardNumber"        => ['required', 'unique:'.Patient::class.',card_no', 'min:9'],
            "sponsor"           => ['required', 'numeric', 'exists:'.Sponsor::class.',id'],
            "phone"             => ['sometimes','required', 'digits:11', 'different:nextOfKinPhone'],
        ];
    }
}
