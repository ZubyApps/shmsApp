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

        if (! empty($params->searchTerm)) {
            return $this->payMethod
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->payMethod
                    ->orderBy($orderBy, $orderDir)
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
                'count'             => $payMethod->payments()->count(),
            ];
         };
    }

    public function list($all = false)
    {   
        // var_dump($all) ;
        if ($all){
            // var_dump('all ran');
            return $this->payMethod->orderBy('name')->get(['id', 'name']);//->toArray();
        }
        return $this->payMethod->orderBy('name')->where('visible', true)->get(['id', 'name'])->toArray();
    }
}