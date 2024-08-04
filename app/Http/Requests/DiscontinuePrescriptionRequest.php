<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiscontinuePrescriptionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->designation?->designation == 'Doctor' || $this->user()->designation?->designation == 'Nurse' || $this->user()->designation?->designation == 'Admin' && $this->user()->designation?->access_level > 2;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            //
        ];
    }
}
