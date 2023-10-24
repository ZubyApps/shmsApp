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
    private $loadTransformer;

    public function __construct(private readonly SponsorCategory $sponsorCategory)
    {
        $this->loadTransformer;
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

        if (! empty($params->searchTerm)) {
            return $this->sponsorCategory
                        ->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->sponsorCategory
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer()
    {
       return $this->loadTransformer = function (SponsorCategory $sponsorCategory) {
            return [
                'id'                => $sponsorCategory->id,
                'name'              => $sponsorCategory->name,
                'description'       => $sponsorCategory->description,
                'consultationFee'   => $sponsorCategory->consultation_fee,
                'payClass'          => $sponsorCategory->pay_class,
                'approval'          => $sponsorCategory->approval === 0 ? 'false' : 'true',
                'billMatrix'        => $sponsorCategory->bill_matrix,
                'balanceRequired'   => $sponsorCategory->balance_required === 0 ? 'false' : 'true',
                'createdAt'         => Carbon::parse($sponsorCategory->created_at)->format('d/m/Y')
            ];
         };
    }
}