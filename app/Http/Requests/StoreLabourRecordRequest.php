<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLabourRecordRequest extends FormRequest
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
            // Demographic and pregnancy details
            'parity' => 'nullable|string|max:255',
            'noOfLivingChildren' => 'nullable|integer|min:0',
            'lmp' => 'nullable|date',
            'edd' => 'nullable|date',
            'ega' => 'nullable|string|max:255',
            'onset' => 'nullable|date',
            'onsetHours' => 'nullable|numeric|min:0',
            'spontaneous' => 'nullable|boolean',
            'induced' => 'nullable|boolean',
            'amniotomy' => 'nullable|boolean',
            'oxytocies' => 'nullable|boolean',
            'mRupturedAt' => 'nullable|date',
            'contractionsBegan' => 'nullable|date',

            // Contraction quality
            'excellent' => 'nullable|boolean',
            'good' => 'nullable|boolean',
            'fair' => 'nullable|boolean',
            'poor' => 'nullable|boolean',

            // Physical measurements
            'fundalHeight' => 'nullable|string|min:0',
            'multiple' => 'nullable|boolean',
            'singleton' => 'nullable|boolean',
            'lie' => 'nullable|string|max:255',
            'presentation' => 'nullable|string|max:255',
            'position' => 'nullable|string|max:255',
            'descent' => 'nullable|string|max:255',
            'foetalHeartRate' => 'nullable|string|min:0',
            'vulva' => 'nullable|string|max:255',
            'vagina' => 'nullable|string|max:255',
            'cervix' => 'nullable|string|max:255',
            'appliedToPp' => 'nullable|boolean',
            'os' => 'nullable|string|max:255',
            'membranesRuptured' => 'nullable|boolean',
            'membranesIntact' => 'nullable|boolean',
            'ppAtO' => 'nullable|string|max:255',
            'stationIn' => 'nullable|string|max:255',
            'caput' => 'nullable|string|max:255',
            'moulding' => 'nullable|string|max:255',
            'sp' => 'nullable|string|max:255',
            'sacralCurve' => 'nullable|string|max:255',
            'forecast' => 'nullable|string|max:255',
            'ischialSpine' => 'nullable|string|max:255',
            'examiner' => 'nullable|string|max:255',
            'designation' => 'nullable|string|max:255',
            'pastObHistory' => 'nullable|string',
            'antenatalHistory' => 'nullable|string',

            // Labor interventions
            'solAmniotomy' => 'nullable|boolean',
            'solAIndication' => 'nullable|string|max:255',
            'solOxytocin' => 'nullable|boolean',
            'solOIndication' => 'nullable|string|max:255',
            'solProstaglandins' => 'nullable|boolean',
            'solPIndication' => 'nullable|string|max:255',
            'dOfLabour' => 'nullable|date',
            'solSpontaneous' => 'nullable|boolean',
            'solAssisted' => 'nullable|boolean',
            'solForceps' => 'nullable|boolean',
            'extraction' => 'nullable|boolean',
            'vacuum' => 'nullable|boolean',
            'internalPodVersion' => 'nullable|boolean',
            'caesareanSection' => 'nullable|boolean',
            'destructiveOperation' => 'nullable|boolean',
            'dOSpecify' => 'nullable|string|max:255',
            'anaesthesia' => 'nullable|boolean',

            // Third stage of labor
            'pSpontaneous' => 'nullable|boolean',
            'cct' => 'nullable|boolean',
            'manualRemoval' => 'nullable|boolean',
            'complete' => 'nullable|boolean',
            'incomplete' => 'nullable|boolean',
            'placentaWeight' => 'nullable|string|min:0',
            'perineumIntact' => 'nullable|boolean',
            '1stDegreeLaceration' => 'nullable|boolean',
            '2ndDegreeLaceration' => 'nullable|boolean',
            '3rdDegreeLaceration' => 'nullable|boolean',
            'episiotomy' => 'nullable|boolean',
            'repairBy' => 'nullable|string|max:255',
            'designationRepair' => 'nullable|string|max:255',
            'noOfSkinSutures' => 'nullable|integer|min:0',
            'bloodLoss' => 'nullable|string|min:0',

            // Neonatal details
            'alive' => 'nullable|boolean',
            'sexes' => 'nullable|string|max:255',
            'babyWeight' => 'nullable|string|min:0',
            'apgarScore1m' => 'nullable|string|min:0|max:10',
            'apgarScore5m' => 'nullable|string|min:0|max:10',
            'freshStillBirth' => 'nullable|boolean',
            'maceratedStillBirth' => 'nullable|boolean',
            'immediateNeonatalDeath' => 'nullable|boolean',
            'malformation' => 'nullable|boolean',

            // Maternal condition
            'mcUterus' => 'nullable|string|max:255',
            'mcBladder' => 'nullable|string|max:255',
            'mcBloodPressure' => 'nullable|string|max:255',
            'mcPulseRate' => 'nullable|string|min:0',
            'mcTemperature' => 'nullable|string|min:0',
            'mcRespiration' => 'nullable|string|min:0',
            'supervisor' => 'nullable|string|max:255',
            'bloodLossTreatment' => 'nullable|string',
            'malformationDetails' => 'nullable|string',
            'accoucheur' => 'nullable|string',

            // Foreign keys
            'visitId' => 'required|exists:visits,id',
        ];
    }

    /**
     * Customize error messages (optional).
     */
    // public function messages(): array
    // {
    //     return [
    //         'visitId.required' => 'The visit ID is required.',
    //         'visitId.exists' => 'The selected visit does not exist.',
    //         'noOfLivingChildren.integer' => 'Number of living children must be an integer.',
    //         'foetalHeartRate.integer' => 'Foetal heart rate must be an integer.',
    //     ];
    // }
}
