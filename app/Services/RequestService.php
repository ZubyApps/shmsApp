<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use Illuminate\Http\Request;

class RequestService
{

    public function getDataTableQueryParameters(Request $request): DataTableQueryParams
    {
        $params = $request->query();

        $orderBy = $params['columns'][$params['order'][0]['column']]['data'];
        $orderDir = $params['order'][0]['dir'];

        return new DataTableQueryParams(
            (int) $params['start'],
            (int) $params['length'],
            $orderBy,
            $orderDir,
            (string)$params['search']['value'],
            (int) $params['draw']
        );
    }

    public function getClientIp(Request $request, array $trustedProxies): ?string
    {
        $serverParams = $request->getServerParams();

        if (in_array($serverParams['REMOTE_ADDR'], $trustedProxies, true)
            && isset($serverParams['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $serverParams['HTTP_X_FORWARDED_FOR']);

            return trim($ips[0]);
        }

        return $serverParams['REMOTE_ADDR'] ?? null;
    }
}
