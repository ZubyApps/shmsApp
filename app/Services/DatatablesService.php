<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatatablesService
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

    public function datatableResponse(callable $transformer, mixed $queryResult, DataTableQueryParams $params): JsonResponse
    {
        return response()->json([
            'data' => array_map($transformer, (array)$queryResult->getIterator()),
            'draw' => $params->draw,
            'recordsTotal' => $queryResult->total(),
            'recordsFiltered' => $queryResult->total()
        ]);     
    }
}