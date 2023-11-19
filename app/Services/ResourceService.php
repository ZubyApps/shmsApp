<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Resource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResourceService
{
    public function __construct(private readonly Resource $resource)
    {
    }

    public function create(Request $data, User $user): Resource
    {
        return $user->resources()->create([
            'name'                      => $data->name,
            'flag'                      => $data->flag,
            'resource_sub_category_id'  => $data->resourceSubCategory,
            'purchase_price'            => $data->purchasePrice,
            'selling_price'             => $data->sellingPrice,
            'reorder_level'             => $data->reOrder,
            'unit_description'          => $data->unitDescription,
            'expiry_date'               => $data->expiryDate ? new Carbon($data->expiryDate) : '',
            // 'stock_level'               => $data->stockLevel,
        ]);
    }

    public function update(Request $data, Resource $resource, User $user): Resource
    {
       $resource->update([
            'name'                      => $data->name,
            'flag'                      => $data->flag,
            'resource_sub_category_id'  => $data->resourceSubCategory,
            'purchase_price'            => $data->purchasePrice,
            'selling_price'             => $data->sellingPrice,
            'reorder_level'             => $data->reOrder,
            'unit_description'          => $data->unitDescription,
            'expiry_date'               => new Carbon($data->expiryDate),
            // 'stock_level'               => $data->stockLevel,
        ]);

        return $resource;
    }

    public function getPaginatedResources(DataTableQueryParams $params)
    {
        $orderBy    = 'name';
        $orderDir   =  'asc';

        if (! empty($params->searchTerm)) {
            return $this->resource
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resourceSubCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resourceSubCategory.resourceCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->resource
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Resource $resource) {
            return [
                'id'                => $resource->id,
                'name'              => $resource->name,
                'flag'              => $resource->flag,
                'category'          => $resource->resourceSubCategory->resourceCategory->name,
                'subCategory'       => $resource->resourceSubCategory->name,
                'unit'              => $resource->unit_description,
                'purchasePrice'     => $resource->purchase_price,
                'sellingPrice'      => $resource->selling_price,
                'reOrder'           => $resource->reorder_level,
                'stock'             => $resource->stock_level,
                'isActive'          => $resource->is_active,
                'expiryDate'        => $resource->expiry_date ? (new Carbon($resource->expiry_date))->format('d/m/y') : 'N/A',
                'expired'           => $resource->expiry_date ? $this->dataDifferenceInDays($resource->expiry_date) : 'N/A',
                'createdBy'         => $resource->user->username,
                'createdAt'         => $resource->created_at->format('d/m/y'),
                'count'             => 0//$resource->resources()->count(),
            ];
         };
    }

    public function dataDifferenceInDays(string $date) {
                $now = Carbon::now();
                $carbonatedDate = new Carbon($date);
                $days = $now->diffInDays($carbonatedDate, false);

                if ($days >= 90){
                    return 'No';
                    }
               
                if ($days > 0 && $days < 90){
                    return 'Soon';
                    }

               if ($days <= 0){
                return 'Yes';
                    }
        
    }

    public function getFormattedList($data)
    {
        if (! empty($data->resource)){
            return $this->resource
                        ->where('name', 'LIKE', '%' . addcslashes($data->resource, '%_') . '%' )
                        ->where('expiry_date', '>', new Carbon())
                        ->where('is_active', true)
                        ->orderBy('name', 'asc')
                        ->get();
        }
           
    }

    public function listTransformer()
    {
        return function (Resource $resource){
            return [
                'id'        => $resource->id,
                'name'      => $resource->name.($resource->flag ? ' - '.$resource->flag : '').($resource->expiry_date && $resource->expiry_date < (new Carbon())->addMonths(3) ? ' - expiring soon - '.$resource->expiry_date : '' ),
                'category'  => $resource->resourceSubCategory->resourceCategory->name
            ];
        };
        
    }
}