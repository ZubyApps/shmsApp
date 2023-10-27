<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Sponsor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SponsorService
{
    public function __construct(private readonly Sponsor $sponsor)
    {
    }

    public function create(Request $data, User $user): Sponsor
    {
        return $user->sponsors()->create([
            'name'                  => $data->name,
            'phone'                 => $data->phone,
            'email'                 => $data->email,
            'registration_bill'     => $data->registrationBill,
            'sponsor_category_id'   => $data->category
        ]);
    }

    public function update(Request $data, Sponsor $sponsor, User $user): Sponsor
    {
       $sponsor->update([
            'name'                  => $data->name,
            'phone'                 => $data->phone,
            'email'                 => $data->email,
            'registration_bill'     => $data->registerationBill,
            'sponsor_category_id'   => $data->category,
            'user_id'               => $user->id

        ]);

        return $sponsor;
    }

    public function getPaginatedSponsors(DataTableQueryParams $params)
    {
        //$orderByParam  =  $params->orderBy === 'created' ? 'created_at' : $params->orderBy;
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        // $orderBy  = in_array($orderByParam, ['created_at', 'name', 'category']) ? $params->orderBy : 'created_at';
        // $orderDir = strtolower($params->orderDir) === 'asc' ? 'asc' : 'desc';

        if (! empty($params->searchTerm)) {
            return $this->sponsor
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->sponsor
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (Sponsor $sponsor) {
            return [
                'id'                => $sponsor->id,
                'name'              => $sponsor->name,
                'phone'             => $sponsor->phone,
                'email'             => $sponsor->email,
                'category'          => $sponsor->sponsorCategory->name,
                'approval'          => $sponsor->sponsorCategory->approval === 0 ? 'false' : 'true',
                'registrationBill'  => $sponsor->registration_bill,
                'createdAt'         => (new Carbon($sponsor->created_at))->format('d/m/Y'),
                'count'             => $sponsor->patients()->count()
            ];
         };
    }
}