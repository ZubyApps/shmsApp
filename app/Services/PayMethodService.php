<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\PayMethod;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayMethodService
{
    public function __construct(private readonly PayMethod $payMethod)
    {
    }

    public function create(Request $data, User $user): PayMethod
    {
        return $user->payMethods()->create([
            'name'          => $data->name,
            'description'   => $data->description,
            'visible'       => $data->visible,
        ]);
    }

    public function update(Request $data, PayMethod $payMethod, User $user): PayMethod
    {
       $payMethod->update([
            'name'          => $data->name,
            'description'   => $data->description,
            'visible'       => $data->visible,
            'user_id'       => $user->id,

        ]);

        return $payMethod;
    }

    public function getPaginatedPayMethods(DataTableQueryParams $params)
    {
        $orderBy    = 'name';
        $orderDir   =  'asc';
        $query      = $this->payMethod->select('id', 'user_id', 'name', 'description', 'visible', 'created_at')
                        ->with(['user:id,username'])
                        ->withExists(['payments as hasPayments']);

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
       return  function (PayMethod $payMethod) {
            return [
                'id'                => $payMethod->id,
                'name'              => $payMethod->name,
                'description'       => $payMethod->description,
                'visible'           => $payMethod->visible,
                'createdBy'         => $payMethod->user->username,
                'createdAt'         => (new Carbon($payMethod->created_at))->format('d/m/y g:ia'),
                'count'             => $payMethod->hasPayments, //payments()->count(),
            ];
         };
    }

    public function list(?bool $all = false, ?bool $collection = false)
    {   
        if ($collection){

            if ($all){
                return $this->payMethod->orderBy('name')->get(['id', 'name']);
            }

            return $this->payMethod->orderBy('name')->where('visible', true)->get(['id', 'name']);
        }
        return $this->payMethod->orderBy('name')->where('visible', true)->get(['id', 'name'])->toArray();
    }
}