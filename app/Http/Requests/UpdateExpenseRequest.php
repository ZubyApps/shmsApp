<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
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
            'description'       => ['required'],
            'expenseCategory'   => ['required', 'integer'],
            'amount'            => ['required'],
            'approvedBy'        => ['required', 'integer'],
            'givenTo'           => ['required'],
        ];
    }
}
