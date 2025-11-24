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
        if ($data->backdate){
            return $user->expenses()->create([
                'description'           => $data->description,
                'expense_category_id'   => $data->expenseCategory,
                'given_to'              => $data->givenTo,
                'amount'                => $data->amount,
                'comment'               => $data->comment,
                'approved_by'           => $data->approvedBy,
                'pay_method_id'         => $data->payMethod,
                'created_at'            => $data->backdate,
            ]);
        }
        return $user->expenses()->create([
            'description'           => $data->description,
            'expense_category_id'   => $data->expenseCategory,
            'given_to'              => $data->givenTo,
            'amount'                => $data->amount,
            'comment'               => $data->comment,
            'approved_by'           => $data->approvedBy,
            'pay_method_id'         => $data->payMethod,
        ]);
    }

    public function update(Request $data, Expense $expense, User $user): Expense
    {
       $expense->update([
            'description'           => $data->description,
            'expense_category_id'   => $data->expenseCategory,
            'given_to'              => $data->givenTo,
            'amount'                => $data->amount,
            'comment'               => $data->comment,
            'approved_by'           => $data->approvedBy,
            'pay_method_id'         => $data->payMethod,
        ]);

        return $expense;
    }

    // public function getPaginatedExpenses(DataTableQueryParams $params, $data)
    // {
    //     $orderBy    = 'created_at';
    //     $orderDir   = 'desc';
    //     $currentDate = new CarbonImmutable();
    //     $query      =   $this->expense::with(['user', 'expenseCategory', 'approvedBy']);

    //     if (! empty($params->searchTerm)) {
    //         $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
    //         if ($data->accessor == 'billing'){
    //                 return $query->whereRelation('user.designation', 'access_level', '<', 5)
    //                         ->where(function (Builder $query) use($searchTerm){
    //                             $query->where('description', 'LIKE', $searchTerm )
    //                                   ->orWhere('comment', 'LIKE', $searchTerm)
    //                                   ->orWhere('created_at', 'LIKE', $searchTerm)
    //                                   ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
    //                                   ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
    //                         })
    //                         ->orderBy($orderBy, $orderDir)
    //                         ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //         if ($data->accessor == 'byExpenseCategory'){

    //             if ($data->startDate && $data->endDate){
    //                 return $query->where('expense_category_id', $data->expenseCategoryId)
    //                         ->where(function (Builder $query) use($searchTerm){
    //                             $query->where('description', 'LIKE', $searchTerm)
    //                                   ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
    //                                   ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
    //                         })
    //                         ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
    //                         ->orderBy($orderBy, $orderDir)
    //                         ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //             }
    
    //             if($data->date){
    //                 $date = new Carbon($data->date);
    //                 return $query->where('expense_category_id', $data->expenseCategoryId)
    //                     ->where(function (Builder $query) use($searchTerm){
    //                         $query->where('description', 'LIKE', $searchTerm)
    //                               ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
    //                               ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
    //                     })
    //                     ->whereMonth('created_at', $date->month)
    //                     ->whereYear('created_at', $date->year)
    //                     ->orderBy($orderBy, $orderDir)
    //                     ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //             }
                
    //             return $query->where('expense_category_id', $data->expenseCategoryId)
    //                     ->where(function (Builder $query) use($searchTerm){
    //                         $query->where('description', 'LIKE', $searchTerm)
    //                               ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
    //                               ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
    //                     })
    //                     ->whereMonth('created_at', $currentDate->month)
    //                     ->whereYear('created_at', $currentDate->year)
    //                     ->orderBy($orderBy, $orderDir)
    //                     ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }          
    //         return $query->where(function (Builder $query) use($searchTerm){
    //                     $query->where('description', 'LIKE', $searchTerm)
    //                         ->orWhere('comment', 'LIKE', $searchTerm)
    //                         ->orWhere('created_at', 'LIKE', $searchTerm)
    //                         ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
    //                         ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
    //                 })
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

    //     }

    //     if ($data->accessor == 'billing'){
    //         return $query->whereRelation('user.designation', 'access_level', '<', 5)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     if ($data->accessor == 'byExpenseCategory'){

    //         if ($data->startDate && $data->endDate){
    //             return $query->where('expense_category_id', $data->expenseCategoryId)
    //                 ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //         if($data->date){
    //             $date = new Carbon($data->date);
    //             return $query->where('expense_category_id', $data->expenseCategoryId)
    //             ->whereMonth('created_at', $date->month)
    //             ->whereYear('created_at', $date->year)
    //             ->orderBy($orderBy, $orderDir)
    //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //         return $query->where('expense_category_id', $data->expenseCategoryId)
    //                 ->whereMonth('created_at', $currentDate->month)
    //                 ->whereYear('created_at', $currentDate->year)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     return $query->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length)); 
    // }

      public function getPaginatedExpenses(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   = 'desc';
        $currentDate = new CarbonImmutable();
        $query      =   $this->expense::with(['user', 'expenseCategory', 'approvedBy']);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            if ($data->accessor == 'billing'){
                    return $query->whereRelation('user.designation', 'access_level', '<', 5)
                            ->where(function (Builder $query) use($searchTerm){
                                $query->where('description', 'LIKE', $searchTerm )
                                      ->orWhere('comment', 'LIKE', $searchTerm)
                                      ->orWhere('created_at', 'LIKE', $searchTerm)
                                      ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                                      ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
                            })
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if ($data->accessor == 'byExpenseCategory'){

                if ($data->startDate && $data->endDate){
                    return $query->where('expense_category_id', $data->expenseCategoryId)
                            ->where(function (Builder $query) use($searchTerm){
                                $query->where('description', 'LIKE', $searchTerm)
                                      ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                                      ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
                            })
                            ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }
    
                if($data->date){
                    $date = new Carbon($data->date);
                    return $query->where('expense_category_id', $data->expenseCategoryId)
                        ->where(function (Builder $query) use($searchTerm){
                            $query->where('description', 'LIKE', $searchTerm)
                                  ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                                  ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
                        })
                        ->whereMonth('created_at', $date->month)
                        ->whereYear('created_at', $date->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
                }
                
                return $query->where('expense_category_id', $data->expenseCategoryId)
                        ->where(function (Builder $query) use($searchTerm){
                            $query->where('description', 'LIKE', $searchTerm)
                                  ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                                  ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
                        })
                        ->whereMonth('created_at', $currentDate->month)
                        ->whereYear('created_at', $currentDate->year)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }          
            return $query->where(function (Builder $query) use($searchTerm){
                        $query->where('description', 'LIKE', $searchTerm)
                            ->orWhere('comment', 'LIKE', $searchTerm)
                            ->orWhere('created_at', 'LIKE', $searchTerm)
                            ->orWhereRelation('user', 'username', 'LIKE', $searchTerm)
                            ->orWhereRelation('expenseCategory', 'name', 'LIKE', $searchTerm);
                    })
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

        }

        if ($data->accessor == 'billing'){
            return $query->whereRelation('user.designation', 'access_level', '<', 5)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->accessor == 'byExpenseCategory'){

            if ($data->startDate && $data->endDate){
                return $query->where('expense_category_id', $data->expenseCategoryId)
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
                return $query->where('expense_category_id', $data->expenseCategoryId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('expense_category_id', $data->expenseCategoryId)
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }
        if ($data->accessor == 'byPayMethod'){

            if ($data->startDate && $data->endDate){
                return $query->where('pay_method_id', $data->payMethodId)
                    ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
                return $query->where('pay_method_id', $data->payMethodId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $query->where('pay_method_id', $data->payMethodId)
                    ->whereMonth('created_at', $currentDate->month)
                    ->whereYear('created_at', $currentDate->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
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
                'payMethod'         => $expense->payMethod?->name,
                'date'              => (new Carbon($expense->created_at))->format('d/m/Y g:ia'),
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

        if ($data->year){

            return DB::table('expenses')
                            ->selectRaw('SUM(amount) as amount, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                            ->whereYear('created_at', $data->year)
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