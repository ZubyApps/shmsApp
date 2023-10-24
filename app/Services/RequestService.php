<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use Illuminate\Http\Request;

class RequestService
{
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
