<?php

namespace App\Http\Requests;

use App\Models\Resource;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class StoreAddResourceStockRequest extends FormRequest
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
            'hmsStock'      => ['required', 'integer'],
            'actualStock'   => ['required', 'integer'],
            'difference'    => ['required', 'integer', 'min:0'],
            'finalQuantity' => ['required', 'integer'],
            'finalStock'    => ['required', 'integer'],
            'sellingPrice'  => ['required', 'numeric'],
            'unitPurchase'  => ['required', 'string'],
            'quantity'      => ['required', 'integer'],
            'comment'       => ['required_unless:difference,0'],
            'expiryDate'    => ['nullable', 'date', 'after_or_equal:'.date('d-m-Y')],
            'resourceId'    => ['required', 'numeric', 'exists:'.Resource::class.',id'],
        ];
    }

    public function messages()
    {
        return [
            'comment.required_unless' => 'The comment field is required when difference is not 0.',
            'difference.min'          => 'Please balance the stock before adding new stock.'
        ];
    }
}
