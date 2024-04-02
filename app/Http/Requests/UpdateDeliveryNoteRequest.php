<?php

namespace App\Http\Requests;

use App\Models\Consultation;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDeliveryNoteRequest extends FormRequest
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
            'date'              => ['date'],
            'timeOfAdmission'   => ['date', 'required'],
            'timeOfDelivery'    => ['date', 'required'],
            'apgarScore'        => ['required'],
            'birthWeight'       => ['required'],
            'modeOfDelivery'    => ['required'],
            'parity'            => ['required'],
            'headCircumference' => ['required'],
            'sex'               => ['required'],
            'ebl'               => ['required'],
        ];
    }
}
