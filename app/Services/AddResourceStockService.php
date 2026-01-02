<?php

declare(strict_types = 1);

namespace App\Services;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AddResourceStock;
use Illuminate\Support\Facades\DB;
use App\DataObjects\DataTableQueryParams;

class AddResourceStockService
{
    public function __construct(private readonly AddResourceStock $addResourceStock)
    {
    }

    public function create(Request $data, User $user): AddResourceStock
    {
        return DB::transaction(function () use ($data, $user) {
            // 1. Prepare Expiry Date once
            $expiryDate = $data->expiryDate 
                ? Carbon::parse($data->expiryDate)->lastOfMonth() 
                : null;

            // 2. Create the Stock Entry
            $addedStock = $user->addResources()->create([
                'resource_id'          => $data->resourceId,
                'hms_stock'            => $data->hmsStock,
                'actual_stock'         => $data->actualStock,
                'difference'           => $data->difference,
                'quantity'             => $data->quantity,
                'final_quantity'       => $data->finalQuantity,
                'final_stock'          => $data->finalStock,
                'comment'              => $data->comment,
                'unit_purchase'        => $data->unitPurchase,
                'unit_description_id'  => $data->unitPurchase,
                'purchase_price'       => $data->purchasePrice,
                'selling_price'        => $data->sellingPrice,
                'expiry_date'          => $expiryDate,
                'resource_supplier_id' => $data->resourceSupplierId,
            ]);

            // 3. Prepare Resource update (DRY approach)
            $resourceUpdate = [
                'unit_description_id' => $data->unitPurchase,
                'purchase_price'      => $data->purchasePrice,
                'selling_price'       => $data->sellingPrice,
                // Atomic increment prevents math errors if two people save at once
                'stock_level'         => DB::raw("stock_level + " . (float)$data->finalQuantity),
            ];

            if ($expiryDate) {
                $resourceUpdate['expiry_date'] = $expiryDate;
            }

            // 4. Update Resource in one go
            $addedStock->resource()->update($resourceUpdate);

            return $addedStock;
        });
    }

    public function getPaginatedAddResourceStocks(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      =  $this->addResourceStock
                        ->select('id', 'user_id', 'resource_id', 'resource_supplier_id', 'hms_stock', 'actual_stock', 'difference', 'quantity', 'final_quantity', 'final_stock', 'comment', 'purchase_price', 'selling_price', 'expiry_date', 'created_at')
                            ->with([
                                'user:id,username',
                                'resourceSupplier:id,company',
                                'resource:id,name'
                            ]);

                            
        if (! empty($params->searchTerm)) {
            return $query->where('created_at', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
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