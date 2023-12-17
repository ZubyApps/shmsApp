<?php

namespace App\Http\Requests;

use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAddResourceStockRequest extends FormRequest
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
            'purchasePrice' => ['required', 'numeric'],
            'sellingPrice'  => ['required', 'numeric'],
            'unitPurchase'  => ['required', 'string'],
            'qty'           => ['required', 'numeric'],
            'resource'      => ['required', 'numeric', 'exists:'.Resource::class.',id'],
        ];
    }
}
