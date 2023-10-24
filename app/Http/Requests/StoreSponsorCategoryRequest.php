<?php

namespace App\Http\Requests;

use App\Models\SponsorCategory;
use Illuminate\Foundation\Http\FormRequest;

class StoreSponsorCategoryRequest extends FormRequest
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
            'name'              => ['required', 'max:255', 'unique:'.SponsorCategory::class],
            'description'       => ['required', 'max:500'],
            'payClass'          => ['required', 'max:10'],
            'approval'          => ['required'],
            'billMatrix'        => ['required'],
            'balanceRequired'   => ['required'],
            'consultationFee'   => ['required']
        ];
    }
}
