<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Resource;
use App\Models\UnitDescription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class UnitDescriptionService
{
    public function __construct(private readonly UnitDescription $unitDescription)
    {
    }

    public function create(Request $data, User $user): UnitDescription
    {
        return $user->unitDescriptions()->create([
            'short_name'    => $data->shortName,
            'long_name'     => $data->longName,
            'description'   => $data->description,
        ]);
    }

    public function update(Request $data, UnitDescription $unitDescription, User $user): UnitDescription
    {
       $unitDescription->update([
            'short_name'   => $data->shortName,
            'long_name'    => $data->longName,
            'description'  => $data->description,
            'user_id'      => $user->id,
        ]);

        return $unitDescription;
    }

    public function getPaginatedWards(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->unitDescription
                        ->where('short_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('long_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->unitDescription
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (UnitDescription $unitDescription) {
            return [
                'id'                => $unitDescription->id,
                'shortName'         => $unitDescription->short_name,
                'longName'          => $unitDescription->long_name,
                'description'       => $unitDescription->description,
                'createdBy'         => $unitDescription->user->username,
                'createdAt'         => (new Carbon($unitDescription->created_at))->format('d/m/Y'),
                'count'             => $unitDescription->resources()->count()
            ];
         };
    }

    public function updateAllDescriptions(UnitDescription $unitDescription)
    {
        $resources         = Resource::where('unit_description', $unitDescription->long_name)->get();
        foreach($resources as $resource){
            $resource->update(['unit_description_id' => $unitDescription->id]);
        }

        return $unitDescription;
    }
}
