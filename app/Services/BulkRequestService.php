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

            // if ($bulkRequest) {
            //     $resource->stock_level = $resource->stock_level - $data->quantity;
            //     $resource->save();
            // }

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
                'qtyApproved'       => $bulkRequest->qty_approved < 1 ? null : $bulkRequest->qty_approved,
                'approvedBy'        => $bulkRequest->approvedBy?->username,
                'qtyDispensed'      => $bulkRequest->qty_dispensed < 1 ? null : $bulkRequest->qty_dispensed,
                'dispensedBy'       => $bulkRequest->dispensedBy?->username,
                'dispensed'         => $bulkRequest->dispensed ? (new Carbon($bulkRequest->dispensed))->format('d/m/y g:ia'): $bulkRequest->dispensed,
                'stock'             => $bulkRequest->resource->stock_level,
                'access'            => request()->user()->designation->access_level > 4
            ];
         };
    }

    public function toggleRequest(Request $data, BulkRequest $bulkRequest, User $user)
    {
        return $bulkRequest->update([
            'qty_approved'  => $data->qty ? $data->qty : 0,
            'approved_by'   => $data->qty ? $user->id : null
        ]);
    }

    public function dispenseRequest(Request $data, BulkRequest $bulkRequest, User $user)
    {
        return DB::transaction(function () use($data, $bulkRequest, $user) {
            $resource = $bulkRequest->resource;
            $qtyDispensed = $bulkRequest->qty_dispensed;

            if ($data->qty){
                if ($qtyDispensed){
                    $resource->stock_level = $resource->stock_level + $qtyDispensed;
                    $resource->save();
                }
                
                $resource->stock_level = $resource->stock_level - $data->qty;
                $resource->save();

            } elseif (!$data->qty) {
                if ($qtyDispensed){
                    $resource->stock_level = $resource->stock_level + $qtyDispensed;
                    $resource->save();
                }
            }

            return $bulkRequest->update([
                'qty_dispensed'     => $data->qty ?? 0,
                'dispensed'         => $data->qty ? new Carbon() : null,
                'dispensed_by'      => $data->qty ? $user->id : null,
            ]);
        });
    }

    public function processDeletion(BulkRequest $bulkRequest)
    {
        return DB::transaction(function () use( $bulkRequest) {
            if ($bulkRequest->qty_dispensed){
                $resource = $bulkRequest->resource;
                $resource->stock_level = $resource->stock_level + $bulkRequest->qty_dispensed;
    
                $resource->save();
            }
    
            return $bulkRequest->destroy($bulkRequest->id);
        });
    }
}