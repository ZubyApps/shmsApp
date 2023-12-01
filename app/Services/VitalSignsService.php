<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Http\Resources\PatientBioResource;
use App\Models\Consultation;
use App\Models\User;
use App\Models\VitalSigns;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class VitalSignsService
{
    public function __construct(private readonly VitalSigns $vitalSigns)
    {
    }

    public function create(Request $data, User $user): VitalSigns
    {

        return $user->vitalSigns()->create([
                "visit_id"          => $data->visitId,
                "temperature"       => $data->temperature,
                "blood_pressure"    => $data->bloodPressure,
                "respiratory_rate"  => $data->respiratoryRate,
                "spO2"              => $data->spO2,
                "pulse_rate"        => $data->pulseRate,
                "weight"            => $data->weight,
                "height"            => $data->height,
        ]);
    }

    public function update(Request $data, VitalSigns $vitalSigns, User $user): VitalSigns
    {
       $vitalSigns->update([
                "visit_id"          => $data->visitId,
                "temperature"       => $data->temperature,
                "blood_pressure"    => $data->bloodPressure,
                "respiratory_rate"  => $data->respiratoryRate,
                "spO2"              => $data->spO2,
                "pulse_rate"        => $data->pulseRate,
                "weight"            => $data->weight,
                "height"            => $data->height,
        ]);
        return $vitalSigns;
    }

    public function getPaginatedVitalSignsByVisit(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';

        if (! empty($params->searchTerm)) {
            return $this->vitalSigns
                        ->Where('visit_id', $data->visitId)
                        ->WhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->vitalSigns
                    ->Where('visit_id', $data->visitId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getVitalSignsTransformer(): callable
    {
       return  function (VitalSigns $vitalSigns) {
            return [
                'id'                => $vitalSigns->id,
                'temperature'       => $vitalSigns->temperature,
                'bloodPressure'     => $vitalSigns->blood_pressure,
                'respiratoryRate'   => $vitalSigns->respiratory_rate,
                'spO2'              => $vitalSigns->spO2,
                'pulseRate'         => $vitalSigns->pulse_rate,
                'weight'            => $vitalSigns->weight,
                'height'            => $vitalSigns->height,
                'created_at'        => (new Carbon($vitalSigns->created_at))->format('d/m/y g:ia'),
                'by'                => $vitalSigns->user->username,
            ];
         };
    }

    public function initiateConsultation(VitalSigns $vitalSigns, Request $request) 
    {
        $vitalSigns->update([
            'doctor'    =>  $request->user()->username
        ]);

        return response()->json(new PatientBioResource($vitalSigns));
    }

    public function getPaginatedConsultedVitalSignss(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->vitalSigns
                    ->where('consulted', true)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->vitalSigns
                    ->where('consulted', true)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getConsultedVitalSignssTransformer(): callable
    {
       return  function (VitalSigns $vitalSigns) {
            return [
                'id'                => $vitalSigns->id,
                'came'              => (new Carbon($vitalSigns->created_at))->format('d/m/Y g:ia'),
                'patient'           => $vitalSigns->patient->card_no.' ' .$vitalSigns->patient->first_name.' '. $vitalSigns->patient->middle_name.' '.$vitalSigns->patient->last_name,
                'doctor'            => $vitalSigns->doctor,
                'diagnosis'         => Consultation::where('vitalSigns_id', $vitalSigns->id)->orderBy('id', 'desc')->first()->icd11_diagnosis,
                'sponsor'           => $vitalSigns->patient->sponsor->name,
                'status'            => Consultation::where('vitalSigns_id', $vitalSigns->id)->orderBy('id', 'desc')->first()->admission_status,
                'patientType'       => $vitalSigns->patient->patient_type,

            ];
         };
    }
}