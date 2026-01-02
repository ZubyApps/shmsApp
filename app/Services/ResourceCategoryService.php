<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\ResourceCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResourceCategoryService
{
    public function __construct(private readonly ResourceCategory $resourceCategory)
    {
    }

    public function create(Request $data, User $user): ResourceCategory
    {
        return $user->resourceCategories()->create([
            'name'          => $data->name,
            'description'   => $data->description,
        ]);
    }

    public function update(Request $data, ResourceCategory $resourceCategory, User $user): ResourceCategory
    {
       $resourceCategory->update([
            'name'          => $data->name,
            'description'   => $data->description,

        ]);

        return $resourceCategory;
    }

    public function getPaginatedResourceCategories(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->resourceCategory->select('id', 'user_id', 'name', 'description', 'created_at')
                        ->with(['user:id,username'])
                        ->withExists(['resourceSubCategories as hasResourceSubCategories']);

        if (! empty($params->searchTerm)) {
            return $query->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (ResourceCategory $resourceCategory) {
            return [
                'id'                => $resourceCategory->id,
                'name'              => $resourceCategory->name,
                'description'       => $resourceCategory->description,
                'createdBy'         => $resourceCategory->user->username,
                'createdAt'         => (new Carbon($resourceCategory->created_at))->format('d/m/y g:ia'),
                'count'             => $resourceCategory->hasResourceSubCategories, //resourceSubCategories()->count(),
            ];
         };
    }
}