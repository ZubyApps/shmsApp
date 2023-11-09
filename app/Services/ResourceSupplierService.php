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
        return $user->resourceSuppliers()->create([
            'company'   => $data->company,
            'person'    => $data->person,
            'phone'     => $data->phone,
            'email'     => $data->email,
            'address'   => $data->address,
        ]);
    }

    public function update(Request $data, ResourceSupplier $resourceSupplier, User $user): ResourceSupplier
    {
       $resourceSupplier->update([
            'company'   => $data->company,
            'person'    => $data->person,
            'phone'     => $data->phone,
            'email'     => $data->email,
            'address'   => $data->address,

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
                'company'           => $resourceSupplier->company,
                'person'            => $resourceSupplier->person,
                'phone'             => $resourceSupplier->phone,
                'email'             => $resourceSupplier->email,
                'address'           => $resourceSupplier->address,
                'createdBy'         => $resourceSupplier->user->username,
                'createdAt'         => (new Carbon($resourceSupplier->created_at))->format('d/m/y g:ia'),
                'count'             => $resourceSupplier->resources()->count(),
            ];
         };
    }

    public function getSupplierList(Request $data)
    {
        if (! empty($data->supplier)) {
            return $this->resourceSupplier
                        ->where('company', 'LIKE', '%' . addcslashes($data->supplier, '%_') . '%' )
                        ->orderBy('company', 'desc')
                        ->select('id', 'company as name')
                        ->get();
        }
    }
}