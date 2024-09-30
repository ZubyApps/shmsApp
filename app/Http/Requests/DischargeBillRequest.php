<?php

namespace App\Http\Requests;

use App\Models\MarkedFor;
use Illuminate\Foundation\Http\FormRequest;

class DischargeBillRequest extends FormRequest
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
            'quantity'  => ['required', 'integer'],
            'wardType'  => ['required'],
            'mark'      => ['required']
        ];
    }

    public function attributes(): array
    {
        return [
            'quantity' => 'days',
        ];
    }
}
