<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UpdateSponsorCategoryRequest extends FormRequest
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
            'name'              => ['required', 'max:255', Rule::unique('sponsor_categories','name')->ignore($request->name, 'name')],
            'description'       => ['required', 'max:500'],
            'payClass'          => ['required', 'max:10'],
            'approval'          => ['required'],
            'billMatrix'        => ['required'],
            'balanceRequired'   => ['required'],
            'consultationFee'   => ['required']
        ];
    }
}
