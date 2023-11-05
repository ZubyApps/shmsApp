<?php

namespace App\Http\Requests;

use App\Models\ResourceCategory;
use Illuminate\Foundation\Http\FormRequest;

class UpdateResourceSubCategoryRequest extends FormRequest
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
            'name'              => ['required'],
            'description'       => ['required'],
            'resourceCategory'  => ['required', 'numeric', 'exists:'.ResourceCategory::class.',id']
        ];
    }
}
