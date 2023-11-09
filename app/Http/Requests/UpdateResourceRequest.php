<?php

namespace App\Http\Requests;

use App\Models\ResourceSubCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateResourceRequest extends FormRequest
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
            'name'                  => ['required', Rule::unique('resources','name')->ignore($request->name, 'name')],
            'expiryDate'            => ['nullable', 'date', 'after_or_equal:'.date('d-m-Y')],
            'resourceSubCategory'   => ['required', 'numeric', 'exists:'.ResourceSubCategory::class.',id'],
        ];
    }
}
