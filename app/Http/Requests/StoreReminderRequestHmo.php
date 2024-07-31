<?php

namespace App\Http\Requests;

use App\Models\Sponsor;
use Illuminate\Foundation\Http\FormRequest;

class StoreReminderRequestHmo extends FormRequest
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
            'monthSentFor'  => ['required', 'date'],
            'dateSent'      => ['required', 'date'],
            "sponsorId"     => ['required', 'numeric', 'exists:'.Sponsor::class.',id'],
        ];
    }
}
