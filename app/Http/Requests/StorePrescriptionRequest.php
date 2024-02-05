<?php

namespace App\Http\Requests;

use App\Models\Consultation;
use App\Models\Resource;
use App\Models\Visit;
use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->designation?->designation == "Doctor" || $this->user()->designation?->access_level > 3;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'resource'      => ['required', 'numeric', 'exists:'.Resource::class.',id'],
            'conId'         => ['required', 'numeric', 'exists:'.Consultation::class.',id'],
            'visitId'       => ['required', 'numeric', 'exists:'.Visit::class.',id'],
            'prescription'  => ['required_if:resourceCategory,Medications'],
            'quantity'      => ['required_unless:resourceCategory,Medications'],
        ];
    }
}
