<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Enum\PayClass;
use App\Models\SponsorCategory;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SponsorCategoryService
{
    public function __construct(private readonly SponsorCategory $sponsorCategory)
    {
    }

    public function create(Request $data, User $user): SponsorCategory
    {
        return $user->sponsorCategories()->create([
            'name' => $data->name,
            'description' => $data->description,
            'pay_class' => PayClass::from($data->payClass),
            'approval'  => filter_var($data->approval, FILTER_VALIDATE_BOOL),
            'bill_matrix' => $data->billMatrix,
            'balance_required' => filter_var($data->balanceRequired, FILTER_VALIDATE_BOOL),
            'consultation_fee' => $data->consultationFee
        ]);
    }

    public function update(Request $data, SponsorCategory $sponsorCategory, User $user): SponsorCategory
    {
       $sponsorCategory->update([
            'name'              => $data->name,
            'description'       => $data->description,
            'pay_class'         => $data->payClass,
            'approval'          => filter_var($data->approval, FILTER_VALIDATE_BOOL),
            'bill_matrix'       => $data->billMatrix,
            'balance_required'  => filter_var($data->balanceRequired, FILTER_VALIDATE_BOOL),
            'consultation_fee'  => $data->consultationFee,
            'user_id'           => $user->id

        ]);

        return $sponsorCategory;
    }

    public function getPaginatedSponsorCategories(DataTableQueryParams $params)
    {
        $orderBy  =  'created_at';
        $orderDir =  'desc';
        $query    = $this->sponsorCategory->select('id', 'name', 'description', 'consultation_fee', 'pay_class', 'approval', 'bill_matrix', 'balance_required', 'created_at')
                        ->withExists(['sponsors as hasSponsor']);
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
       return  function (SponsorCategory $sponsorCategory) {
            return [
                'id'                => $sponsorCategory->id,
                'name'              => $sponsorCategory->name,
                'description'       => $sponsorCategory->description,
                'consultationFee'   => $sponsorCategory->consultation_fee,
                'payClass'          => $sponsorCategory->pay_class,
                'approval'          => $sponsorCategory->approval === 0 ? 'false' : 'true',
                'billMatrix'        => $sponsorCategory->bill_matrix,
                'balanceRequired'   => $sponsorCategory->balance_required === 0 ? 'false' : 'true',
                'createdAt'         => (new Carbon($sponsorCategory->created_at))->format('d/m/Y'),
                'count'             => $sponsorCategory->hasSponsor,
            ];
         };
    }
}