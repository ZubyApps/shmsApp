<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\ResourceSubCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResourceSubCategoryService
{
    public function __construct(private readonly ResourceSubCategory $resourceSubCategory)
    {
    }

    public function create(Request $data, User $user): ResourceSubCategory
    {
        return $user->resourceSubCategories()->create([
            'name'                  => $data->name,
            'description'           => $data->description,
            'resource_category_id'  => $data->resourceCategory,
        ]);
    }

    public function update(Request $data, ResourceSubCategory $resourceSubCategory, User $user): ResourceSubCategory
    {
       $resourceSubCategory->update([
            'name'                  => $data->name,
            'description'           => $data->description,
            'resource_category_id'  => $data->resourceCategory,
        ]);

        return $resourceSubCategory;
    }

    public function getPaginatedResourceCategories(DataTableQueryParams $params)
    {
        $orderBy    = 'name';
        $orderDir   =  'asc';
        $query      = $this->resourceSubCategory->select('id', 'resource_category_id', 'user_id', 'name', 'description', 'created_at')
                            ->with([
                                'resourceCategory:id,name',
                                'user:id,username'
                            ])
                            ->withExists(['resources as hasResources']);
        if (! empty($params->searchTerm)) {
            return $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('resourceCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (ResourceSubCategory $resourceSubCategory) {
            return [
                'id'                => $resourceSubCategory->id,
                'name'              => $resourceSubCategory->name,
                'description'       => $resourceSubCategory->description,
                'category'          => $resourceSubCategory->resourceCategory->name,
                'createdBy'         => $resourceSubCategory->user->username,
                'createdAt'         => (new Carbon($resourceSubCategory->created_at))->format('d/m/y g:ia'),
                'count'             => $resourceSubCategory->hasResources,//resources()->count(),
            ];
         };
    }
}