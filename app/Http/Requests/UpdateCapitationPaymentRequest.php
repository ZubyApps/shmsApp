<?php

namespace App\Http\Requests;

use App\Models\Sponsor;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCapitationPaymentRequest extends FormRequest
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
            'monthPaidFor' => ['required', 'date'],
            'amount_paid'  => ['required', 'numeric'],
            "sponsor"      => ['required', 'numeric', 'exists:'.Sponsor::class.',id'],
        ];
    }
}
