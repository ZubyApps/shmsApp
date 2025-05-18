<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class StorePartographRequest extends FormRequest
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
        // Base rules for all observations
        $baseRules = [
            'labourRecordId' => 'required|exists:labour_records,id',
            'recordedAt' => 'nullable|date|before_or_equal:'.date('d-m-Y H:i:s'),
            'parameterType' => 'required|in:uterine_contractions,fetal_heart_rate,blood_pressure,pulse,temperature,urine,oxytocin,cervical_dilation,descent,caput,position,moulding,oxytocin,fluid,drug', // Add all parameter_types
            'value' => 'required|array',
        ];

        // Parameter-specific rules for value
        $valueRules = match ($this->parameterType) {
            'uterine_contractions' => [
                'value.count_per_10min' => 'required|integer|min:0',
                'value.lasting_seconds' => 'required|integer|min:0',
                'value.strength' => 'required|in:Weak,Moderate,Strong',
            ],
            'fetal_heart_rate' => [
                'value.bpm' => 'required|integer|min:60|max:200',
            ],
            'cervical_dilation' => [
                'value.cm' => 'required|integer|min:1|max:10',
            ],
            'descent' => [
                'value.fifths' => 'required|string|min:1|max:5',
            ],
            'pulse' => [
                'value.bpm' => 'required|string',
            ],
            'temperature' => [
                'value.celsius' => 'required|string',
            ],
            'caput' => [
                'value.degree' => 'required|string',
            ],
            'position' => [
                'value.position' =>  'required|in:OA,OP,LOA,ROA,LOT,ROT',
            ],
            'moulding' => [
                'value.degree' =>  'required|string',
            ],
            'oxytocin' => [
                'value.dosage' =>  'required|string',
            ],
            'fluid' => [
                'value.status' =>  'required|string',
            ],
            'drug' => [
                'value.type' =>  'required|string',
            ],
            'blood_pressure' => [
                'value.systolic' => 'required|integer|min:0',
                'value.diastolic' => 'required|integer|min:0',
            ],
            'urine' => [
                'value.volume' => 'required|integer|min:0',
                'value.protein' => 'required|string',
                'value.glucose' => 'required|string',
            ],
            'oxytocin' => [
                'value.dose' => 'required|numeric|min:0',
                'value.unit' => 'required|in:mU/min',
            ],
            default => throw new ValidationException(
                validator($this->all(), []),
                response()->json(['parameter_type' => ['Invalid parameter type: ' . $this->parameter_type]], 422)
            ),
        };

        return array_merge($baseRules, $valueRules);
    }

    public function attributes(): array
    {
        return [
            'value.cm' => 'Cervical Dilation',
            'value.fifths' => 'Descent',
            'value.rate' => 'Fetal Heart Rate',
        ];
    }
}
