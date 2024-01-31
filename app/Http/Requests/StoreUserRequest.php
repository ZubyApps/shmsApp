<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->designation?->access_level > 3;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'firstName'             => ['required', 'string', 'max:255'],
            'middleName'            => ['string', 'max:255'],
            'lastName'              => ['required', 'string', 'max:255'],
            'username'              => ['required', 'string', 'max:255', 'unique:' . User::class],
            'phoneNumber'           => ['required', 'numeric', 'digits:11', 'unique:' . User::class.',phone_number'],
            'email'                 => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'address'               => ['string', 'max:255'],
            'highestQualification'  => ['required', 'string', 'max:255'],
            'dateOfBirth'           => ['required', 'date'],
            'sex'                   => ['required', 'string', 'max:255'],
            'maritalStatus'         => ['required', 'string', 'max:255'],
            'stateOfOrigin'         => ['required', 'string', 'max:255'],
            'nextOfKin'             => ['required', 'string', 'max:255'],
            'nextOfKinPhone'        => ['required', 'numeric', 'digits:11'],
            // 'nextOfKinPhone'        => ['required', 'numeric', 'min:11'],
            'nextOfKinRship'        => ['required', 'string'],
            'dateOfEmployment'      => ['required', 'nullable', 'date'],
            'dateOfExit'            => ['nullable', 'date'],
            'department'            => ['nullable', 'string'],
            'password'              => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }
}
