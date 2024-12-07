<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\AddResourceStock;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AddResourceStockService
{
    public function __construct(private readonly AddResourceStock $addResourceStock)
    {
    }

    public function create(Request $data, User $user): AddResourceStock
    {
        $addedStock = $user->addResources()->create([
            'resource_id'           => $data->resourceId,
            'hms_stock'             => $data->hmsStock,
            'actual_stock'          => $data->actualStock,
            'difference'            => $data->difference,
            'quantity'              => $data->quantity,
            'final_quantity'        => $data->finalQuantity,
            'final_stock'           => $data->finalStock,
            'comment'               => $data->comment,
            'unit_purchase'         => $data->unitPurchase,
            'unit_description_id'   => $data->unitPurchase,
            'purchase_price'        => $data->purchasePrice,
            'selling_price'         => $data->sellingPrice,
            'expiry_date'           => $data->expiryDate ? (new Carbon($data->expiryDate))->lastOfMonth() : null,
            'resource_supplier_id'  => $data->resourceSupplierId,
        ]);

        if ($data->expiryDate){
            $addedStock->resource()->update([
                    'stock_level'           => $addedStock->resource->stock_level + $data->finalQuantity,
                    'unit_description_id'   => $data->unitPurchase, 
                    'purchase_price'        => $data->purchasePrice, 
                    'selling_price'         => $data->sellingPrice, 
                    'expiry_date'           => (new Carbon($data->expiryDate))->lastOfMonth(), 
                ]);
                return $addedStock;
            }
        
        $addedStock->resource()->update([
            'stock_level'       => $addedStock->resource->stock_level + $data->finalQuantity,
            'unit_description_id'  => $data->unitPurchase, 
            'purchase_price'    => $data->purchasePrice, 
            'selling_price'     => $data->sellingPrice, 
        ]);
        return $addedStock;
    }

    public function getPaginatedAddResourceStocks(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->addResourceStock
                        ->where('created_at', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->addResourceStock
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (AddResourceStock $addResourceStock) {
            return [
                'id'                => $addResourceStock->id,
                'resource'          => $addResourceStock->resource?->name,
                'hmsStock'          => $addResourceStock->hms_stock,
                'actualStock'       => $addResourceStock->actual_stock,
                'diff'              => $addResourceStock->difference,
                'qty'               => $addResourceStock->quantity,
                'finalQty'          => $addResourceStock->final_quantity,
                'finalStock'        => $addResourceStock->final_stock,
                'comment'           => $addResourceStock->comment,
                'purchasePrice'     => $addResourceStock->purchase_price,
                'sellingPrice'      => $addResourceStock->selling_price,
                'expiryDate'        => (new Carbon($addResourceStock->expiry_date))->format('d/m/y'),
                'supplier'          => $addResourceStock->resourceSupplier?->company,
                'createdBy'         => $addResourceStock->user->username,
                'createdAt'         => (new Carbon($addResourceStock->created_at))->format('d/m/y'),
            ];
         };
    }
}