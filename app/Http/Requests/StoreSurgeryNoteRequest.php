<?php

namespace App\Http\Requests;

use App\Models\Consultation;
use App\Models\Visit;
use Illuminate\Foundation\Http\FormRequest;

class StoreSurgeryNoteRequest extends FormRequest
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
            'date'              => ['required'],
            'typeOfOperation'   => ['required'],
            'typeOfAneasthesia' => ['required'],
            'surgeon'           => ['required'],
            'scrubNurse'        => ['required'],
            'surgicalProcedure' => ['required'],
            'surgeonsNotes'     => ['required'],
            'aneasthetistsNotes'=> ['required'],
            'postOperationNotes'=> ['required'],
            'immediatePostOp'   => ['required'],
            'visitId'           => ['required', 'numeric', 'exists:'.Visit::class.',id'],
        ];
    }
}
