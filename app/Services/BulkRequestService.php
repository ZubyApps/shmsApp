<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\BulkRequest;
use App\Models\Resource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BulkRequestService
{
    public function __construct(private readonly BulkRequest $bulkRequest, private readonly Resource $resource)
    {
        
    }

    public function create(Request $data, Resource $resource, User $user): BulkRequest
    {
        return DB::transaction(function () use($data, $resource, $user) {

            $bulkRequest = $user->bulkRequests()->create([
                'resource_id'       => $resource->id,
                'quantity'          => $data->quantity,
                'department'        => $data->department,
                'note'              => $data->note,
                'cost_price'        => $resource->cost_price * $data->quantity,
                'selling_price'     => $resource->selling_price * $data->quantity,
            ]);

            if ($bulkRequest) {
                $resource->stock_level = $resource->stock_level - $data->quantity;
                $resource->save();
            }

            return $bulkRequest;
        });
    }

    public function getLabBulkRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->bulkRequest
                        ->where('department', 'Lab')
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->bulkRequest
                    ->where('department', 'Lab')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length)); 
    }

    public function getNursesBulkRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->bulkRequest
                        ->where('department', 'Nurses')
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->bulkRequest
                    ->where('department', 'Nurses')
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length)); 
    }

    public function getPharmacyBulkRequests(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        if (! empty($params->searchTerm)) {
            return $this->bulkRequest
                        ->where(function (Builder $query) use($params) {
                            $query->whereRelation('resource', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                            ->orWhereRelation('user', 'username', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                        })
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->bulkRequest
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length)); 
    }

    public function getBulkRequestTransformer(): callable
    {
       return  function (BulkRequest $bulkRequest) {
            return [
                'id'                => $bulkRequest->id,
                'date'              => (new Carbon($bulkRequest->created_at))->format('d/m/y g:ia'),
                'item'              => $bulkRequest->resource->name,
                'quantity'          => $bulkRequest->quantity,
                'dept'              => $bulkRequest->department,
                'requestedBy'       => $bulkRequest->user->username,
                'note'              => $bulkRequest->note,
                'approvedBy'        => $bulkRequest->approvedBy?->username,
                'dispensedBy'       => $bulkRequest->dispensedBy?->username,
                'dispensed'         => $bulkRequest->dispensed ? (new Carbon($bulkRequest->dispensed))->format('d/m/y g:ia'): $bulkRequest->dispensed,
            ];
         };
    }
}