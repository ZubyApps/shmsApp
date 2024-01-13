<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\User;
use App\Models\VitalSigns;
use Carbon\Carbon;
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
                "bmi"               => $data->bmi,
                "note"               => $data->note,
        ]);
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

    public function getVitalSignsChartData($data)
    {
        return $this->vitalSigns
                    ->Where('visit_id', $data->visitId)
                    ->orderBy('created_at', 'asc')
                    ->get();
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
                'bmi'               => $vitalSigns->bmi ?? '',
                'created_at'        => (new Carbon($vitalSigns->created_at))->format('d/m/y g:ia'),
                'by'                => $vitalSigns->user->username,
                'note'            => $vitalSigns->note,
            ];
         };
    }
}
