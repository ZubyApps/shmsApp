<?php

namespace App\Http\Requests;

use App\Models\Sponsor;
use App\Models\SponsorCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateSponsorRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        return [
            'name' => ['required', Rule::unique('sponsors','name')->ignore($request->name, 'name')],
            'phone' => ['required', 'digits:11', Rule::unique('sponsors','phone')->ignore($request->phone, 'phone')],
            'email' => ['nullable', 'email', Rule::unique('sponsors','email')->ignore($request->email, 'email')],
            'registration'  => ['sometimes', 'required', 'numeric'],
            'category' => ['required', 'numeric', 'exists:'.SponsorCategory::class.',id']
        ];
    }
}
