<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMortuaryServiceRequest extends FormRequest
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
            'deceasedName' => 'required',
            'sex'   => 'required',
            'depositor' => 'required',
            'depositorAddress' => 'required',
            'depositorPhone' => 'required',
            'depositorRship' => 'required',
            'altCollectorName' => 'required',
            'altCollectorPhone' => 'required'
        ];
    }
}
