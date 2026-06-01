<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Enum\CommunicationType;
use App\Models\Communication;
use Carbon\Carbon;

class CommunicationService
{
    public function __construct(
            private readonly Communication $communication,
            )
        {
        }


    public function getPaginatedSmses(DataTableQueryParams $params)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';

        $query = $this->communication->select('id', 'recipient_name', 'recipient_contact', 'network', 'message', 'units_deducted', 'status', 'created_at', 'message_type')
                    ->where('type_id', CommunicationType::SMS);

        if (! empty($params->searchTerm)) {
            $searchTermRaw = trim($params->searchTerm);
            $searchTerm = addcslashes($searchTermRaw, '%_') . '%';

            $query->where('recipient_name', 'LIKE', $searchTerm);
        }

        return $query->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getLoadTransformer(): callable
    {
       return  function (Communication $communication) {
            return [
                'id'                => $communication->id,
                'recipient'         => $communication->recipient_name,
                'contact'           => $communication->recipient_contact,
                'network'           => $communication->network,
                'message'           => $communication->message,
                'units'             => $communication->units_deducted,
                'status'            => $communication->status,
                'messageType'       => $communication->message_type,
                'createdAt'         => (new Carbon($communication->created_at))->format('d/m/Y g:ia'),
            ];
         };
    }
}