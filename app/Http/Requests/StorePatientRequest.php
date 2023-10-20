<?php

namespace App\Http\Requests;

use App\Models\Patient;
use App\Models\Sponsor;
use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        //return false;
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
            "address"           => ['nullable', 'max:500'],
            "bloodGroup"        => ['nullable'],
            "cardNumber"        => ['required', 'unique:'.Patient::class.',card_no'],
            "dateOfBirth"       => ['required'],
            "email"             => ['nullable', 'unique:'.Patient::class],
            "ethnicGroup"       => ['nullable'],
            "firstName"         => ['required'],
            "genotype"          => ['nullable'],
            "knownConditions"   => ['nullable'],
            "lastName"          => ['required'],
            "maritalStatus"     => ['required'],
            "middleName"        => ['nullable'],
            "nationality"       => ['nullable'],
            "nextOfKin"         => ['required'],
            "nextOfKinPhone"    => ['required', 'digits:11'],
            "nextOfKinRship"    => ['required'],
            "occupation"        => ['nullable'],
            "phone"             => ['required', 'digits:11'],
            "registrationBill"  => ['nullable'],
            "religion"          => ['nullable'],
            "sex"               => ['required'],
            "sponsor"           => ['required', 'numeric', 'exists:'.Sponsor::class.',id'],
            "staffId"           => ['nullable'],
            "stateOrigin"       => ['nullable'],
            "stateResidence"    => ['required'],
        ];
    }
}
