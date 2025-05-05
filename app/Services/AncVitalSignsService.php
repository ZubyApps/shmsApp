<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\AncVitalSigns;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AncVitalSignsService
{
    public function __construct(private readonly AncVitalSigns $ancVitalSigns)
    {
    }

    public function create(Request $data, User $user): AncVitalSigns
    {

        return $user->ancVitalSigns()->create([
                "antenatal_registeration_id"    => $data->ancRegId,
                "ho_fundus"                     => $data->heightOfFundus,
                "p_position"                    => $data->presentationAndPosition,
                "roppt_brim"                    => $data->relationOfPresentingPartToBrim,
                "fh_rate"                       => $data->fetalHeartRate,
                "urine_protein"                 => $data->urineProtein,
                "urine_glucose"                 => $data->urineGlucose,
                "blood_pressure"                => $data->bloodPressure,
                "weight"                        => $data->weight,
                "hb"                            => $data->hb,
                "oedema"                        => $data->oedema,
                "remarks"                       => $data->remarks,
                "return"                        => $data->return,
        ]);
    }

    public function getPaginatedAncVitalSignsByVisit(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        
        if (! empty($params->searchTerm)) {
            return $this->ancVitalSigns
                        ->Where('antenatal_registeration_id', $data->ancRegId)
                        ->WhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->ancVitalSigns
                    ->Where('antenatal_registeration_id', $data->ancRegId)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getAncVitalSignsChartData($data)
    {
        return $this->ancVitalSigns
                    ->Where('antenatal_registeration_id', $data->ancRegId)
                    ->orderBy('created_at', 'asc')
                    ->get();
    }

    public function getAncVitalSignsTransformer(): callable
    {
       return  function (AncVitalSigns $ancVitalSigns) {
            return [
                'id'                                => $ancVitalSigns->id,
                'created_at'                        => (new Carbon($ancVitalSigns->created_at))->format('d/m/y g:ia'),
                'urineProtein'                      => $ancVitalSigns->urine_protein,
                'urineGlucose'                      => $ancVitalSigns->urine_glucose,
                'bloodPressure'                     => $ancVitalSigns->blood_pressure,
                'weight'                            => $ancVitalSigns->weight,
                'remarks'                           => $ancVitalSigns->remarks,
                'by'                                => $ancVitalSigns->user->username,
                'visitType'                       => $ancVitalSigns->antenatalRegisteration->visit->visit_type
            ];
         };
    }
}
