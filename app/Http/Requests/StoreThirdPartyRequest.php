<?php

namespace App\Http\Requests;

use App\Models\ThirdParty;
use Illuminate\Foundation\Http\FormRequest;

class StoreThirdPartyRequest extends FormRequest
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
            'fullName'  => ['required', 'unique:'.ThirdParty::class.',full_name'],
            'shortName' => ['required', 'unique:'.ThirdParty::class.',full_name'],
            'phone'     => ['required', 'digits:11', 'unique:'.ThirdParty::class],
            'email'     => ['nullable', 'email', 'unique:'.ThirdParty::class],
            'address'   => ['required'],
        ];
    }
}
