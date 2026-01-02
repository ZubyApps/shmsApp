<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\ResourceSponsor;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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
            'sponsor_category_id'   => $data->category,
            'category_name'         => SponsorCategory::findOrFail($data->category)->name,
            'max_pay_days'          => $data->maxPayDays,
            'flag'                  => $data->flagSponsor,
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
            'category_name'         => SponsorCategory::findOrFail($data->category)->name,
            'user_id'               => $user->id,
            'max_pay_days'          => $data->maxPayDays,
            'flag'                  => $data->flagSponsor,

        ]);

        return $sponsor;
    }

    public function getPaginatedSponsors(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->sponsor->select('id', 'sponsor_category_id', 'user_id', 'name', 'phone', 'email', 'registration_bill', 'max_pay_days', 'created_at')
                        ->with([
                            'sponsorCategory:id,name,approval',
                            'resourceSponsors' => function ($query){
                                $query->select('id', 'user_id', 'sponsor_id', 'resource_id', 'selling_price')
                                    ->with([
                                        'resource:id,name,category,sub_category,unit_description',
                                        'createdByUser:id,username'
                                    ]);
                            },
                            'user:id,username'
                        ])
                        ->withExists(['patients as hasPatients']);

        if (! empty($params->searchTerm)) {
            $searchTerm = '%' . addcslashes($params->searchTerm, '%_') . '%';
            return $query->where('name', 'LIKE', $searchTerm)
                        ->orWhere('category_name', 'LIKE', $searchTerm)
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
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
                'maxPayDays'        => $sponsor->max_pay_days,
                'flag'              => $sponsor->flag,
                'createdBy'         => $sponsor->user->username,
                'createdAt'         => (new Carbon($sponsor->created_at))->format('d/m/Y'),
                'count'             => $sponsor->hasPatients,
                'showHmo'           => auth()->user()->designation->designation === 'HMO Officer',
                'showAll'           => auth()->user()->designation->access_level > 4,
                'resources'         => $sponsor->resourceSponsors->map(fn(ResourceSponsor $pivot)=> [
                    'id'            => $pivot->resource->id,
                    'name'          => $pivot->resource->name,
                    'sellingPrice'  => $pivot->selling_price,
                    'category'      => $pivot->resource->category,
                    'subCategory'   => $pivot->resource->sub_category,
                    'unit'          => $pivot->resource->unit_description,
                    'createdBy'     => $pivot->createdByUser?->username,
                ])
            ];
         };
    }

    public function HmoSponsorList($data)
    {
        if (! empty($data->fullId)){
            $searchTerm = '%' . addcslashes($data->fullId, '%_') . '%';

            return $this->sponsor
                        ->where(function (Builder $query) {
                            $query->where('category_name', 'HMO')
                            ->orWhere('category_name', 'NHIS')
                            ->orWhere('category_name', 'Retainership');
                        })
                        ->Where('name', 'LIKE', $searchTerm)
                        ->orderBy('created_at', 'asc')
                        ->get(['name']);
        }      
    }

    public function listTransformer()
    {
        return function (Sponsor $sponsor){
            return [
                'name' => $sponsor->name,
            ];
        };
    }
}
