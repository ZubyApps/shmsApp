<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Resource;
use App\Models\ResourceSubCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
            'marked_for_id'             => $data->markedFor ? strtolower($data->markedFor) : null,
            'category'                  => ResourceSubCategory::findOrFail($data->resourceSubCategory)->resourceCategory->name,
            'sub_category'              => ResourceSubCategory::findOrFail($data->resourceSubCategory)->name,
            'resource_sub_category_id'  => $data->resourceSubCategory,
            'medication_category_id'    => $data->medicationCategory,
            'purchase_price'            => $data->purchasePrice,
            'selling_price'             => $data->sellingPrice,
            'reorder_level'             => $data->reOrder,
            'location'                  => $data->location,
            'unit_description_id'       => $data->unitDescription,
            'expiry_date'               => $data->expiryDate ? (new Carbon($data->expiryDate))->lastOfMonth() : null,
        ]);
    }

    public function update(Request $data, Resource $resource): Resource
    {
       $resource->update([
            'name'                      => $data->name,
            'flag'                      => $data->flag,
            'marked_for_id'             => $data->markedFor ? strtolower($data->markedFor) : null,
            'category'                  => ResourceSubCategory::findOrFail($data->resourceSubCategory)->resourceCategory->name,
            'sub_category'              => ResourceSubCategory::findOrFail($data->resourceSubCategory)->name,
            'resource_sub_category_id'  => $data->resourceSubCategory,
            'medication_category_id'    => $data->medicationCategory,
            'purchase_price'            => $data->purchasePrice,
            'selling_price'             => $data->sellingPrice,
            'reorder_level'             => $data->reOrder,
            'location'                  => $data->location,
            'unit_description_id'       => $data->unitDescription,
            'expiry_date'               => $data->expiryDate ? (new Carbon($data->expiryDate))->lastOfMonth() : null,
        ]);

        return $resource;
    }

    public function getPaginatedResources(DataTableQueryParams $params)
    {
        $orderBy    = 'name';
        $orderDir   =  'asc';
        $query = $this->resource->select('id', 'marked_for_id', 'medication_category_id', 'unit_description_id', 'user_id', 'marked_for', 'name', 'flag', 'purchase_price', 'selling_price', 'reorder_level', 'stock_level', 'location', 'is_active', 'expiry_date', 'created_at', 'category', 'sub_category')
                    ->with([
                        'markedFor:id,name',
                        'medicationCategory:id,name',
                        'unitDescription:id,short_name',
                        'user:id,username',
                    ])
                    ->withExists([
                        'prescriptions as hasPrescriptions',
                        'bulkRequests as hasBulkRequests',
                        'addResources as hasAddResources'
                    ]);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where('name', 'LIKE', $searchTerm)
                        ->orWhere('sub_category', 'LIKE', $searchTerm)
                        ->orWhere('category', 'LIKE', $searchTerm)
                        ->orWhereRelation('medicationCategory', 'name', 'LIKE', $searchTerm)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Resource $resource) {
            return [
                'id'                => $resource->id,
                'name'              => $resource->name,
                'flag'              => $resource->flag,
                'markedFor'         => $resource->markedFor?->name,
                'category'          => $resource->category,
                'subCategory'       => $resource->sub_category,
                'medicationCategory'=> $resource->medicationCategory?->name,
                'unit'              => $resource->unitDescription?->short_name,
                'purchasePrice'     => $resource->purchase_price,
                'sellingPrice'      => $resource->selling_price,
                'reOrder'           => $resource->reorder_level,
                'stock'             => $resource->stock_level,
                'location'          => $resource->location,
                'isActive'          => $resource->is_active,
                'expiryDate'        => $resource->expiry_date ? (new Carbon($resource->expiry_date))->format('d/m/y') : 'N/A',
                'expired'           => $resource->expiry_date ? $this->dataDifferenceInDays($resource->expiry_date) : 'N/A',
                'createdBy'         => $resource->user->username,
                'createdAt'         => $resource->created_at->format('d/m/y'),
                'count'             => $resource->hasPrescriptions ?? $resource->hasBulkRequests ?? $resource->hasAddResources,
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
            return $this->resource->select('id', 'name', 'expiry_date', 'sub_category', 'category', 'flag', 'stock_level')
                        ->where('name', 'LIKE', '%' . addcslashes($data->resource, '%_') . '%' )
                        ->where('is_active', true)
                        // ->whereNot('flag','LIKE', '%' . addcslashes($data->sponsorCat, '%_') . '%' )
                        ->orderBy('name', 'asc')
                        ->get();
        }      
    }

    public function getFormattedList2($data)
    {
        if (! empty($data->resource)){
            return $this->resource->select('id', 'name', 'expiry_date', 'sub_category', 'category', 'flag', 'stock_level', 'selling_price')
                        ->where('name', 'LIKE', '%' . addcslashes($data->resource, '%_') . '%' )
                        // ->where('is_active', true)
                        // ->whereNot('flag','LIKE', '%' . addcslashes($data->sponsorCat, '%_') . '%' )
                        ->orderBy('name', 'asc')
                        ->get();
        }      
    }

    public function getBulkList($data)
    {
        if (! empty($data->resource)){
            return $this->resource->select('id', 'name', 'expiry_date', 'sub_category', 'category', 'flag', 'stock_level')
                            ->where('name', 'LIKE', '%' . addcslashes($data->resource, '%_') . '%' )
                            ->where(function (Builder $query) use($data) {
                                $query->where('category', 'Consumables')
                                ->orWhereRelation('markedFor', 'name', 'LIKE', $data->dept);
                            })
                            ->where('is_active', true)
                            ->orderBy('name', 'asc')
                            ->get();
        }    
    }

    public function getTheatreMatch($data)
    {
        return $this->resource->select('id', 'name', 'expiry_date', 'sub_category', 'category', 'flag', 'stock_level')
                        ->where('name', 'LIKE', '%' . addcslashes($data->resource, '%_') . '%' )
                        ->where(function (Builder $query) use($data) {
                            $query->whereDoesntHave('markedFor')
                                ->orWhereRelation('markedFor', 'name', '!=', 'theatre');
                        })
                        ->where('is_active', true)
                        ->orderBy('name', 'asc')
                        ->get();  
    }

    public function getEmergencyList($data)
    {
        if (! empty($data->resource)){
            return $this->resource->select('id', 'name', 'expiry_date', 'sub_category', 'category', 'flag', 'stock_level')
                        ->where('name', 'LIKE', '%' . addcslashes($data->resource, '%_') . '%' )
                        ->where(function(Builder $query) {
                            $query->where('category', 'Medications')
                            ->orWhere('category', 'Consumables')
                            ->orWhere('category', 'Investigations')
                            ->orWhere('category', 'Medical Services');
                        })
                        ->where('is_active', true)
                        ->whereNot('flag','LIKE', '%' . addcslashes($data->sponsorCat, '%_') . '%' )
                        ->orderBy('name', 'asc')
                        ->get();
        }
           
    }

    public function getRequestsList($data)
    {
        if (! empty($data->resource)){
            return $this->resource->select('id', 'name', 'expiry_date', 'sub_category', 'category', 'flag', 'stock_level')
                            ->where('name', 'LIKE', '%' . addcslashes($data->resource, '%_') . '%' )
                            ->where(function (Builder $query) use($data) {
                                $query->where('category', 'Investigations');
                            })
                            ->where('is_active', true)
                            ->orderBy('name', 'asc')
                            ->get();
        }    
    }

    public function getHospitalServicesList($data)
    {
        if (! empty($data->resource)){
            return $this->resource->select('id', 'name', 'expiry_date', 'sub_category', 'category', 'flag', 'stock_level')
                            ->where('name', 'LIKE', '%' . addcslashes($data->resource, '%_') . '%' )
                            ->where(function (Builder $query) use($data) {
                                $query->where('category', 'Hospital Services');
                            })
                            ->where('is_active', true)
                            ->orderBy('name', 'asc')
                            ->get();
        }    
    }

    public function listTransformer()
    {
        return function (Resource $resource){

            return [
                'id'                    => $resource->id,
                'nameWithIndicators'    => $resource->nameWithIndicators(),
                'name'                  => $resource->name,
                'category'              => $resource->category,
                'subCategory'           => $resource->sub_category,
                'flag'                  => $resource->flag,
                'price'                 => $resource->selling_price
            ];
        };   
    }

    public function listTransformer1()
    {
        return function (Resource $resource){
            return [
                'id'        => $resource->id,
                'name'      => $resource->name,
                'category'  => $resource->category,
                'stock'     => $resource->stock_level,
            ];
        };
    }
}