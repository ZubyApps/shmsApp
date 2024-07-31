<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->designation?->access_level > 4;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    { 
        return [
            'firstName1'             => ['required', 'string', 'max:255'],
            'lastName1'              => ['required', 'string', 'max:255'],
            'username1'              => ['required', 'string', 'max:255', 'unique:' . User::class.',username,' . $this->user->id],
            'phoneNumber1'           => ['required', 'numeric', 'digits:11', 'unique:' . User::class.',phone_number,' . $this->user->id],
            'email1'                 => ['required', 'string', 'email', 'max:255', 'unique:' . User::class.',email,' . $this->user->id],
            'address1'               => ['string', 'max:255'],
            'highestQualification1'  => ['required', 'string', 'max:255'],
            'dateOfBirth1'           => ['required', 'date'],
            'sex1'                   => ['required', 'string', 'max:255'],
            'maritalStatus1'         => ['required', 'string', 'max:255'],
            'stateOfOrigin1'         => ['required', 'string', 'max:255'],
            'nextOfKin1'             => ['required', 'string', 'max:255'],
            'nextOfKinPhone1'        => ['required', 'numeric', 'digits:11'],
            'nextOfKinRship1'        => ['required', 'string'],
            'dateOfEmployment1'      => ['required', 'nullable', 'date'],
            'dateOfExit1'            => ['nullable', 'date'],
            'department1'            => ['nullable', 'string'],
            'password'               => ['nullable', 'confirmed', Password::min(8)->mixedCase()],
        ];
    }
}
