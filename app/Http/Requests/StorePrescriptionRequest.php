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
            'resource'      => ['required', 'integer', 'exists:'.Resource::class.',id'],
            // 'visitId'       => ['required', 'integer', 'exists:'.Visit::class.',id'],
            'visitId'       => ['required_without_all:walkInId,mortuaryServiceId', 'integer', 'exists:'.Visit::class.',id'],
            'dose'          => ['required_if:resourceCategory,Medications', 'nullable', 'numeric', 'min:0.00001'],
            'days'          => ['required_if:resourceCategory,Medications', 'nullable', 'integer', 'min:1'],
            'unit'          => ['required_if:resourceCategory,Medications'],
            'frequency'     => ['required_if:resourceCategory,Medications'],
            'quantity'      => ['required_unless:resourceCategory,Medications', 'nullable', 'integer', 'min:1'],
            'note'          => ['required_if:chartable,true'],
            // 'doc'           => ['required_without:conId'],
            // 'conId'         => ['required_without:doc', 'nullable', 'integer', 'exists:'.Consultation::class.',id'],
            'conId'         => ['required_without_all:visitId,mortuaryServiceId,walkInId', 'nullable', 'integer', 'exists:'.Consultation::class.',id'],
            'walkInId'               => ['required_without_all:visitId,mortuaryServiceId'],
            'mortuaryServiceId'      => ['required_without_all:visitId,walkInId'],
        ];
    }
}
