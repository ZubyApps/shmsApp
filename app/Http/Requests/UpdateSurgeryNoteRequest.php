<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSurgeryNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
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
            'assistantSurgeon'  => ['required'],
            'scrubNurses'       => ['required'],
            'surgicalProcedure' => ['required'],
            'surgeonsNotes'     => ['required'],
            'postOpNotes'       => ['required'],
            'immediatePostOp'   => ['required']
        ];
    }
}
