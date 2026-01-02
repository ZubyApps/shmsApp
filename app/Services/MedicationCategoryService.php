<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\MedicationCategory;
use App\Models\ResourceCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MedicationCategoryService
{
    public function __construct(private readonly MedicationCategory $medicationCategory)
    {
    }

    public function create(Request $data, User $user): MedicationCategory
    {
        return $user->medicationCategories()->create([
            'name'          => $data->name,
            'description'   => $data->description,
        ]);
    }

    public function update(Request $data, MedicationCategory $medicationCategory, User $user): MedicationCategory
    {
       $medicationCategory->update([
            'name'          => $data->name,
            'description'   => $data->description,
            'user_id'       => $user->id
        ]);

        return $medicationCategory;
    }

    public function getPaginatedResourceCategories(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->medicationCategory->select('id', 'name', 'description', 'user_id', 'created_at')
                        ->withExists(['resources as hasResources']);;

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
       return  function (MedicationCategory $medicationCategory) {
            return [
                'id'                => $medicationCategory->id,
                'name'              => $medicationCategory->name,
                'description'       => $medicationCategory->description,
                'createdBy'         => $medicationCategory->user->username,
                'createdAt'         => (new Carbon($medicationCategory->created_at))->format('d/m/y g:ia'),
                'count'             => $medicationCategory->hasResources,
            ];
         };
    }

    public function getList()
    {
        return $this->medicationCategory
                ->orderBy('name')
                ->get(['id', 'name'])->toJson();
    }
}