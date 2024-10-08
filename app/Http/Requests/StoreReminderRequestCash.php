<?php

namespace App\Http\Requests;

use App\Models\Visit;
use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequestCash extends FormRequest
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
            "dateSet"       => ['required', 'date'],
            "comment"       => ['required', 'max:255'],
            "payDate"       => ['required', 'date'],
            "visitId"       => ['required', 'numeric', 'exists:'.Visit::class.',id'],

        ];
    }
}
