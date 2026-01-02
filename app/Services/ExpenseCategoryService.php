<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\ExpenseCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseCategoryService
{
    public function __construct(private readonly ExpenseCategory $expenseCategory)
    {
    }

    public function create(Request $data, User $user): ExpenseCategory
    {
        return $user->expenseCategories()->create([
            'name'          => $data->name,
            'description'   => $data->description,
        ]);
    }

    public function update(Request $data, ExpenseCategory $expenseCategory, User $user): ExpenseCategory
    {
       $expenseCategory->update([
            'name'          => $data->name,
            'description'   => $data->description,
            'user_id'       => $user->id
        ]);

        return $expenseCategory;
    }

    public function getPaginatedExpenseCategories(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   = 'desc';

        $query = $this->expenseCategory->select('id', 'name', 'description', 'created_at', 'user_id')
                    ->with([
                        'user:id,username',
                    ])
                    ->withExists(['expenses as hasExpenses']);

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
       return  function (ExpenseCategory $expenseCategory) {
            return [
                'id'            => $expenseCategory->id,
                'name'          => $expenseCategory->name,
                'description'   => $expenseCategory->description,
                'createdBy'     => $expenseCategory->user->username,
                'createdAt'     => (new Carbon($expenseCategory->created_at))->format('d/m/Y gi:a'),
                'count'         => $expenseCategory->hasExpenses
            ];
         };
    }
}