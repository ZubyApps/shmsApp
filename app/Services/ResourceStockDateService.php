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
            'date'          => new Carbon($data->date),
            'description'   => $data->description,
            'participants'  => $data->participants,
        ]);
    }

    public function update(Request $data, ResourceStockDate $resourceStockDate, User $user): ResourceStockDate
    {
       $resourceStockDate->update([
            'date'          => new Carbon($data->date),
            'description'   => $data->description,
            'participants'  => $data->participants,

        ]);

        return $resourceStockDate;
    }

    public function getPaginatedResourceStockDates(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $query      = $this->resourceStockDate->select('id', 'user_id', 'date', 'description', 'participants', 'reset', 'created_at')
                            ->with(['user:id,username']);
        if (! empty($params->searchTerm)) {
            return $query->where('date', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getLoadTransformer(): callable
    {
       return  function (ResourceStockDate $resourceStockDate) {
            return [
                'id'                => $resourceStockDate->id,
                'date'              => (new Carbon($resourceStockDate->date))->format('d/m/y g:ia'),
                'description'       => $resourceStockDate->description,
                'participants'      => $resourceStockDate->participants,
                'reset'             => $resourceStockDate->reset,
                'createdBy'         => $resourceStockDate->user->username,
                'createdAt'         => $resourceStockDate->created_at->format('d/m/y g:ia'),
            ];
         };
    }
}