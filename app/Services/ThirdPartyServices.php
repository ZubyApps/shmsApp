<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\ThirdParty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ThirdPartyServices
{
    public function __construct(private readonly ThirdParty $thirdParty)
    {
    }

    public function create(Request $data, User $user): ThirdParty
    {
        return $user->thirdParties()->create([
            'full_name'             => $data->fullName,
            'short_name'            => $data->shortName,
            'phone'                 => $data->phone,
            'address'               => $data->address,
            'email'                 => $data->email,
            'comment'               => $data->comment,
        ]);
    }

    public function update(Request $data, ThirdParty $thirdParty, User $user): ThirdParty
    {
       $thirdParty->update([
            'full_name'             => $data->fullName,
            'short_name'            => $data->shortName,
            'phone'                 => $data->phone,
            'address'               => $data->address,
            'email'                 => $data->email,
            'comment'               => $data->comment,
            'user_id'               => $user->id
        ]);

        return $thirdParty;
    }

    public function getPaginatedThirdParty(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->thirdParty->select('id', 'user_id', 'full_name', 'short_name', 'address', 'phone', 'email', 'created_at', 'delisted', 'comment')
                        ->with(['user:id,username'])
                        ->withExists('thirdPartyServies as hasThirdPartyServies');

        if (! empty($params->searchTerm)) {
            return $query->where('full_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhere('short_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (ThirdParty $thirdParty) {
            return [
                'id'                => $thirdParty->id,
                'fullName'          => $thirdParty->full_name,
                'shortName'         => $thirdParty->short_name,
                'address'           => $thirdParty->address,
                'phone'             => $thirdParty->phone,
                'email'             => $thirdParty->email,
                'comment'           => $thirdParty->comment,
                'createdBy'         => $thirdParty->user->username,
                'createdAt'         => (new Carbon($thirdParty->created_at))->format('d/m/Y'),
                'delisted'          => $thirdParty->delisted,
                'count'             => $thirdParty->hasThirdPartyServies,//->count()
            ];
         };
    }

    public function getAllListedThirdParties()
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        return $this->thirdParty
                    ->where('delisted', false)
                    ->orderBy($orderBy, $orderDir);
    }
}
