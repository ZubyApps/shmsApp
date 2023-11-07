<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\AddResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AddResourceStockService
{
    public function __construct(private readonly AddResource $addResource)
    {
    }

    public function create(Request $data, User $user): AddResource
    {
        return $user->addResources()->create([
            'resource_id'           => $data->resourceId,
            'quantity'              => $data->quantity,
            'unit_purchase'         => $data->unitPurchase,
            'purchase_price'        => $data->purchasePrice,
            'selling_price'         => $data->sellingPrice,
            'expiry_date'           => $data->expiryDate,
            'resource_supplier_id'  => $data->supplier,
        ]);
    }

    public function update(Request $data, AddResource $addResource, User $user): AddResource
    {
       $addResource->update([
            'resource_id'           => $data->resource,
            'quantity'              => $data->qty,
            'unit_purchase'         => $data->unitPurchase,
            'purchase_price'        => $data->purchasePrice,
            'selling_price'         => $data->sellingPrice,
            'expiry_date'           => $data->expiryDate,
            'resource_supplier_id'  => $data->supplier,

        ]);

        return $addResource;
    }

    public function getPaginatedAddResourceStocks(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->addResource
                        ->whereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->addResource
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (AddResource $addResource) {
            return [
                'id'                => $addResource->id,
                'resource'          => $addResource->resource->name,
                'qty'               => $addResource->quantity,
                'purchasePrice'     => $addResource->purchase_price,
                'sellingPrice'      => $addResource->selling_price,
                'expiryDate'        => (new Carbon($addResource->expiry_date))->format('d/m/y'),
                'supplier'          => $addResource->resource_supplier,
                'createdBy'         => $addResource->user->username,
                'createdAt'         => (new Carbon($addResource->created_at))->format('d/m/y'),
                // 'count'             => $addResource->resourceSubCategories()->count(),
            ];
         };
    }
}