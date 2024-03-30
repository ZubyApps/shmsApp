<?php

namespace App\Http\Requests;

use App\Models\Resource;
use App\Models\ResourceSubCategory;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreResourceRequest extends FormRequest
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
            'name'                  => ['required', 'unique:'.Resource::class],
            'flag'                  => ['required'],
            'unitDescription'       => ['required'],
            'reOrder'               => ['required'],
            'expiryDate'            => ['nullable', 'date', 'after_or_equal:'.date('d-m-Y')],
            'resourceSubCategory'   => ['required', 'numeric', 'exists:'.ResourceSubCategory::class.',id'],
        ];
    }
}
