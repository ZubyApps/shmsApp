<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateThirdPartyRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        return [
            'fullName'  => ['required', Rule::unique('third_parties','full_name')->ignore($request->fullName, 'full_name')],
            'shortName' => ['required', Rule::unique('third_parties','short_name')->ignore($request->shortName, 'short_name')],
            'phone'     => ['required', 'digits:11', Rule::unique('third_parties','phone')->ignore($request->phone, 'phone')],
            'email'     => ['nullable', 'email', Rule::unique('third_parties','email')->ignore($request->email, 'email')],
            'address'   => ['required'],
        ];
    }
}
