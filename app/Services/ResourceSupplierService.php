<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\ResourceSupplier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResourceSupplierService
{
    public function __construct(private readonly ResourceSupplier $resourceSupplier)
    {
    }

    public function create(Request $data, User $user): ResourceSupplier
    {
        return $user->resourceCategories()->create([
            'name'          => $data->name,
            'description'   => $data->description,
        ]);
    }

    public function update(Request $data, ResourceSupplier $resourceSupplier, User $user): ResourceSupplier
    {
       $resourceSupplier->update([
            'name'          => $data->name,
            'description'   => $data->description,

        ]);

        return $resourceSupplier;
    }

    public function getPaginatedResourceSupplierStocks(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->resourceSupplier
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->resourceSupplier
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (ResourceSupplier $resourceSupplier) {
            return [
                'id'                => $resourceSupplier->id,
                'name'              => $resourceSupplier->name,
                'description'       => $resourceSupplier->description,
                'createdBy'         => $resourceSupplier->user->username,
                'createdAt'         => (new Carbon($resourceSupplier->created_at))->format('d/m/y g:ia'),
                'count'             => $resourceSupplier->resourceSubCategories()->count(),
            ];
         };
    }
}