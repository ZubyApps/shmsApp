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
        $payload = [
            'description'         => $data->description,
            'expense_category_id' => $data->expenseCategory,
            'given_to'            => $data->givenTo,
            'amount'              => $data->amount,
            'comment'             => $data->comment,
            'approved_by'         => $data->approvedBy,
            'pay_method_id'       => $data->payMethod,
        ];

        // Conditionally add the backdate only if it exists
        if ($data->backdate) {
            $payload['created_at'] = Carbon::parse($data->backdate);
        }

        return $user->expenses()->create($payload);
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

    // public function getPaginatedExpenses1(DataTableQueryParams $params, Request $data)
    // {
    //     $orderBy        = 'created_at';
    //     $orderDir       = 'desc';
    //     $currentDate    = new CarbonImmutable();
    //     $query          =   $this->expense->select('id', 'expense_category_id', 'user_id', 'pay_method_id', 'approved_by', 'amount', 'description', 'given_to', 'comment', 'created_at')
    //                         ->with(['user:id,username', 'expenseCategory:id,name', 'approvedBy:id,username', 'payMethod:id,name']);

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
    //     if ($data->accessor == 'byPayMethod'){

    //         if ($data->startDate && $data->endDate){
    //             return $query->where('pay_method_id', $data->payMethodId)
    //                 ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //         if($data->date){
    //             $date = new Carbon($data->date);
    //             return $query->where('pay_method_id', $data->payMethodId)
    //             ->whereMonth('created_at', $date->month)
    //             ->whereYear('created_at', $date->year)
    //             ->orderBy($orderBy, $orderDir)
    //             ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //         }

    //         return $query->where('pay_method_id', $data->payMethodId)
    //                 ->whereMonth('created_at', $currentDate->month)
    //                 ->whereYear('created_at', $currentDate->year)
    //                 ->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    //     }

    //     return $query->orderBy($orderBy, $orderDir)
    //                 ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length)); 
    // }

    public function getPaginatedExpenses(DataTableQueryParams $params, Request $data)
    {
        $currentDate = new CarbonImmutable();
        
        $query = $this->expense->query()
            ->select(['expenses.id', 'expenses.expense_category_id', 'expenses.user_id', 'expenses.pay_method_id', 'expenses.approved_by', 'expenses.amount', 'expenses.description', 'expenses.given_to', 'expenses.comment', 'expenses.created_at'])
            // Use joins for high-speed filtering, keep 'with' for the transformer
            ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->with(['user:id,username', 'expenseCategory:id,name', 'approvedBy:id,username', 'payMethod:id,name']);

        // Filter by Accessor Type
        $query->when($data->accessor === 'billing', function ($q) {
            $q->whereRelation('user.designation', 'access_level', '<', 5);
        })
        ->when($data->accessor === 'byExpenseCategory', function ($q) use ($data, $currentDate) {
            $q->where('expense_category_id', $data->expenseCategoryId);
            $this->applyDateFilters($q, $data, $currentDate);
        })
        ->when($data->accessor === 'byPayMethod', function ($q) use ($data, $currentDate) {
            $q->where('pay_method_id', $data->payMethodId);
            $this->applyDateFilters($q, $data, $currentDate);
        });

        // Global Search (Applied once, regardless of accessor)
        if (!empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            $startsWith = addcslashes($params->searchTerm, '%_') . '%';
            $query->where(function ($q) use ($searchTerm, $startsWith) {
                $q->where('expenses.created_at', 'LIKE', $startsWith)
                ->orWhere('expenses.description', 'LIKE', $searchTerm)
                ->orWhere('expenses.comment', 'LIKE', $searchTerm)
                ->orWhere('users.username', 'LIKE', $searchTerm)
                ->orWhere('expense_categories.name', 'LIKE', $searchTerm);
            });
        }

        return $query->orderBy('expenses.created_at', 'desc')
            ->paginate($params->length, ['*'], 'page', floor($params->start / $params->length) + 1);
    }

    // Helper to avoid repeating date logic
    private function applyDateFilters(Builder $query, object $data, mixed $currentDate) {
        if ($data->startDate && $data->endDate) {
            return $query->whereBetween('expenses.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59']);
        }
        
        $date = $data->date ? new Carbon($data->date) : $currentDate;
    
        // This replaces whereMonth and whereYear
        $startOfMonth = $date->startOfMonth()->toDateTimeString();
        $endOfMonth   = $date->endOfMonth()->toDateTimeString();

        return $query->whereBetween('expenses.created_at', [$startOfMonth, $endOfMonth]);
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

    // public function getExpensesByDate(Request $data)
    // {
    //     $currentDate = new CarbonImmutable();

    //      if ($data->accessor){
    //         if ($data->date){
    //             $date = new CarbonImmutable($data->date);
    //             return DB::table('expenses')
    //                         ->selectRaw('SUM(expenses.amount) as totalExpense, pay_methods.id as id')
    //                         ->leftJoin('pay_methods', 'expenses.pay_method_id', '=', 'pay_methods.id')
    //                         ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
    //                         ->leftJoin('designations', 'users.id', '=', 'designations.user_id')
    //                         ->groupBy('id')
    //                         ->where('designations.access_level', '<', 5)
    //                         ->where('pay_methods.name', 'Cash')
    //                         ->whereMonth('expenses.created_at', $date->month)
    //                         ->whereYear('expenses.created_at', $date->year)
    //                         ->first();
    //         }

    //         return DB::table('expenses')
    //                         ->selectRaw('SUM(expenses.amount) as totalExpense, pay_methods.id as id')
    //                         ->leftJoin('pay_methods', 'expenses.pay_method_id', '=', 'pay_methods.id')
    //                         ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
    //                         ->leftJoin('designations', 'users.id', '=', 'designations.user_id')
    //                         ->groupBy('id')
    //                         ->where('designations.access_level', '<', 5)
    //                         ->where('pay_methods.name', 'Cash')
    //                         ->whereMonth('expenses.created_at', $currentDate->month)
    //                         ->whereYear('expenses.created_at', $currentDate->year)
    //                         ->first();
    //     }

    //     if ($data->date){
    //         return DB::table('expenses')
    //                         ->selectRaw('SUM(expenses.amount) as totalExpense, pay_methods.id as id')
    //                         ->leftJoin('pay_methods', 'expenses.pay_method_id', '=', 'pay_methods.id')
    //                         ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
    //                         ->leftJoin('designations', 'users.id', '=', 'designations.user_id')
    //                         ->groupBy('id')
    //                         ->where('designations.access_level', '<', 5)
    //                         ->where('pay_methods.name', 'Cash')
    //                         ->whereDate('expenses.created_at', $data->date)
    //                         ->first();
    //     }

    //     return DB::table('expenses')
    //                         ->selectRaw('SUM(expenses.amount) as totalExpense, pay_methods.id as id')
    //                         ->leftJoin('pay_methods', 'expenses.pay_method_id', '=', 'pay_methods.id')
    //                         ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
    //                         ->leftJoin('designations', 'users.id', '=', 'designations.user_id')
    //                         ->groupBy('id')
    //                         ->where('designations.access_level', '<', 5)
    //                         ->where('pay_methods.name', 'Cash')
    //                         ->whereDate('expenses.created_at', $currentDate->format('Y-m-d'))
    //                         ->first();
    // }

    public function getExpensesByDate(Request $data)
    {
        $currentDate = new CarbonImmutable();
        $dateInput = $data->date ? new CarbonImmutable($data->date) : $currentDate;

        // 1. Base Query for all scenarios
        $query = DB::table('expenses')
            ->selectRaw('SUM(expenses.amount) as totalExpense, pay_methods.id as id')
            ->leftJoin('pay_methods', 'expenses.pay_method_id', '=', 'pay_methods.id')
            ->leftJoin('users', 'expenses.user_id', '=', 'users.id')
            ->leftJoin('designations', 'users.id', '=', 'designations.user_id')
            ->where('designations.access_level', '<', 5)
            ->where('pay_methods.name', 'Cash')
            ->groupBy('id');

        // 2. Apply Date Logic (Range-based to keep indexes alive)
        if ($data->accessor) {
            // Monthly Range
            $start = $dateInput->startOfMonth()->toDateTimeString();
            $end   = $dateInput->endOfMonth()->toDateTimeString();
        } else {
            // Daily Range
            $start = $dateInput->startOfDay()->toDateTimeString();
            $end   = $dateInput->endOfDay()->toDateTimeString();
        }

        return $query->whereBetween('expenses.created_at', [$start, $end])->first();
    }

    // public function totalYearlyExpense(Request $data)
    // {
    //     $currentDate = new Carbon();

    //     if ($data->year){

    //         return DB::table('expenses')
    //                         ->selectRaw('SUM(amount) as amount, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
    //                         ->whereYear('created_at', $data->year)
    //                         ->groupBy('month_name', 'month')
    //                         ->orderBy('month')
    //                         ->get();
    //     }

    //     return DB::table('expenses')
    //                     ->selectRaw('SUM(amount) as amount, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
    //                     ->whereYear('created_at', $currentDate->year)
    //                     ->groupBy('month_name', 'month')
    //                     ->orderBy('month')
    //                     ->get();
    // }

    public function totalYearlyExpense(Request $data)
    {
        $year = $data->year ?? date('Y');
        
        // Define the boundaries of the year
        $startOfYear = "$year-01-01 00:00:00";
        $endOfYear   = "$year-12-31 23:59:59";

        return DB::table('expenses')
            ->selectRaw('SUM(amount) as amount, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
            ->whereBetween('created_at', [$startOfYear, $endOfYear])
            ->groupBy('month', 'month_name')
            ->orderBy('month')
            ->get();
    }
}