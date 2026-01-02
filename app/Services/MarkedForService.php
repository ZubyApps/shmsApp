<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MarkedFor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MarkedForService
{
    public function __construct(private readonly MarkedFor $markedFor)
    {
    }

    public function create(Request $data, User $user): MarkedFor
    {
        return $user->markedFors()->create([
            'name'            => strtolower($data->name),
            'description'     => $data->description,
        ]);
    }

    public function update(Request $data, MarkedFor $markedFor, User $user): MarkedFor
    {
       $markedFor->update([
            'name'            => strtolower($data->name),
            'description'     => $data->description,
            'user_id'         => $user->id
        ]);

        return $markedFor;
    }

    public function getPaginatedMarkedFors(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        $query = $this->markedFor->select('id', 'name', 'description', 'created_at', 'user_id')
                    ->with([
                        'user:id,username',
                    ])
                    ->withExists(['resources as hasResources']);

        if (! empty($params->searchTerm)) {
            return $query
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('description', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (MarkedFor $markedFor) {
            return [
                'id'                => $markedFor->id,
                'name'              => $markedFor->name,
                'description'       => $markedFor->description,
                'createdBy'         => $markedFor->user->username,
                'createdAt'         => (new Carbon($markedFor->created_at))->format('d/m/Y'),
                'count'             => $markedFor->hasResources
            ];
         };
    }

    public function getOne($searchTerm)
    {
        return $this->markedFor
                    ->where('name', 'LIKE', '%' . addcslashes($searchTerm, '%_') . '%' )
                    ->get('id', 'name');
    }
}
