<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Expense;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            // 'user_id'       => $user->id
        ]);

        return $expense;
    }

    public function getPaginatedExpenses(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   = 'desc';
        $currentDate = new CarbonImmutable();
        // dd($data->accessor == 'byExpenseCategory');
        if (! empty($params->searchTerm)) {

            if ($data->accessor == 'billing'){
                    return $this->expense
                            ->whereRelation('user.designation', 'access_level', '<', 5)
                            ->where(function (Builder $query) use($params){
                                $query->whereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('expenseCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->accessor == 'byExpenseCategory'){
                if ($data->startDate && $data->endDate){
                    return $this->expense
                            ->where('expense_category_id', $data->expenseCategoryId)
                            ->where(function (Builder $query) use($params){
                                $query->whereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhere('description', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }
    
                if($data->date){
                    $date = new Carbon($data->date);
                    return $this->expense
                        ->where('expense_category_id', $data->expenseCategoryId)
                        ->where(function (Builder $query) use($params){
                            $query->whereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('description', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }
                
                return $this->expense
                        ->where('expense_category_id', $data->expenseCategoryId)
                        ->where(function (Builder $query) use($params){
                            $query->whereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhere('description', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->whereMonth('created_at', $currentDate->month)
                        ->whereYear('created_at', $currentDate->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
                

            return $this->expense
                    ->where(function (Builder $query) use($params){
                        $query->whereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('expenseCategory', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

        }

        if ($data->accessor == 'billing'){
            return $this->expense
                    ->whereRelation('user.designation', 'access_level', '<', 5)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->accessor == 'byExpenseCategory'){

            if ($data->startDate && $data->endDate){
                return $this->expense
                    ->where('expense_category_id', $data->expenseCategoryId)
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
                return $this->expense
                ->where('expense_category_id', $data->expenseCategoryId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->expense
                    ->where('expense_category_id', $data->expenseCategoryId)
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
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
                'givenBy'           => $expense->user->username,
                'approvedBy'        => $expense->approvedBy->username,
                'comment'           => $expense->comment,
                'date'              => (new Carbon($expense->created_at))->format('d/m/Y gi:a'),
            ];
         };
    }

    public function getExpensesByDate($data)
    {
        $currentDate = new CarbonImmutable();

        if ($data->date){
            return DB::table('expenses')
                            ->selectRaw('SUM(expenses.amount) as totalExpense')
                            ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
                            ->leftJoin('designations', 'users.id', '=', 'designations.user_id')
                            ->where('designations.access_level', '<', 5)
                            ->whereDate('expenses.created_at', $data->date)
                            ->first();
        }

        return DB::table('expenses')
                            ->selectRaw('SUM(expenses.amount) as totalExpense')
                            ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
                            ->leftJoin('designations', 'users.id', '=', 'designations.user_id')
                            ->where('designations.access_level', '<', 5)
                            ->whereDate('expenses.created_at', $currentDate->format('Y-m-d'))
                            ->first();
    }

    public function totalYearlyExpense($data)
    {
        $currentDate = new Carbon();

        if ($data->date){
            $date = new Carbon($data->date);

            return DB::table('expenses')
                            ->selectRaw('SUM(amount) as amount, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                            ->whereYear('created_at', $date->year)
                            ->groupBy('month_name', 'month')
                            ->orderBy('month')
                            ->get();
        }

        return DB::table('expenses')
                        ->selectRaw('SUM(amount) as amount, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                        ->whereYear('created_at', $currentDate->year)
                        ->groupBy('month_name', 'month')
                        ->orderBy('month')
                        ->get();
    }
}