<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\ResourceStockDate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ResourceStockDateService
{
    public function __construct(private readonly ResourceStockDate $resourceStockDate)
    {
    }

    public function create(Request $data, User $user): ResourceStockDate
    {
        return $user->resourceStockDates()->create([
            'description'          => $data->description,
            'date'                 => $data->date,
        ]);
    }

    public function update(Request $data, ResourceStockDate $resourceStockDate, User $user): ResourceStockDate
    {
       $resourceStockDate->update([
            'description'   => $data->description,
            'date'          => $data->date,

        ]);

        return $resourceStockDate;
    }

    public function getPaginatedResourceStockDates(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->resourceStockDate
                        ->where('date', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->resourceStockDate
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (ResourceStockDate $resourceStockDate) {
            return [
                'id'                => $resourceStockDate->id,
                'date'              => (new Carbon($resourceStockDate->date))->format('d/m/y g:ia'),
                'description'       => $resourceStockDate->description,
                'createdBy'         => $resourceStockDate->user->username,
                'createdAt'         => (new Carbon($resourceStockDate->created_at))->format('d/m/y g:ia'),
            ];
         };
    }
}