<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExpenseService
{
    public function __construct(private readonly Expense $expense)
    {
    }

    public function create(Request $data, User $user): Expense
    {
        return $user->expenses()->create([
            'description'           => $data->description,
            'expense_category_id'   => $data->expenseCategory,
            'given_to'              => $data->givenTo,
            'amount'                => $data->amount,
            'comment'               => $data->comment,
            'approved_by'           => $data->approvedBy,
        ]);
    }

    public function update(Request $data, Expense $expense, User $user): Expense
    {
       $expense->update([
            'description'   => $data->description,
            'given_to'      => $data->givenTo,
            'amount'        => $data->amount,
            'comment'       => $data->comment,
            'approved_by'   => $data->approvedBy,
            'user_id'       => $user->id
        ]);

        return $expense;
    }

    public function getPaginatedExpenses(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   = 'desc';

        if (! empty($params->searchTerm)) {
            return $this->expense
                        ->whereRelation('user', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->expense
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

        
    }

    public function getLoadTransformer(): callable
    {
       return  function (Expense $expense) {
            return [
                'id'                => $expense->id,
                'description'       => $expense->description,
                'category'          => $expense->expenseCategory->name,
                'amount'            => $expense->amount,
                'givenTo'           => $expense->given_to,
                'approvedBy'        => $expense->approvedBy->username,
                'givenBy'           => $expense->user->username,
                'comment'           => $expense->comment,
                'date'              => (new Carbon($expense->created_at))->format('d/m/Y gi:a'),
            ];
         };
    }
}