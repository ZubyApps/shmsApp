<?php

namespace App\Http\Requests;

use App\Models\Sponsor;
use App\Models\SponsorCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSponsorRequest extends FormRequest
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
            'name'          => ['required', 'unique:'.Sponsor::class],
            'phone'         => ['required', 'digits:11', 'unique:'.Sponsor::class],
            'email'         => ['nullable', 'email', 'unique:'.Sponsor::class],
            'registration'  => ['sometimes', 'required', 'numeric'],
            'category'      => ['required', 'numeric', 'exists:'.SponsorCategory::class.',id']
        ];
    }
}
