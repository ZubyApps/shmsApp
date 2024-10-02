<?php

namespace App\Http\Controllers;

use App\Models\BulkRequest;
use App\Http\Requests\StoreBulkRequestRequest;
use App\Http\Requests\UpdateBulkRequestRequest;
use App\Models\Resource;
use App\Services\BulkRequestService;
use App\Services\DatatablesService;
use App\Services\ResourceService;
use Illuminate\Http\Request;

class BulkRequestController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly BulkRequestService $bulkRequestService,
        private readonly ResourceService $resourceService
    )
    {  
    }

    public function listBulk(Request $request)
    {
        $items = $this->resourceService->getBulkList($request);

        $listTransformer = $this->resourceService->listTransformer1();

        return array_map($listTransformer, (array)$items->getIterator());

    }

    public function store(StoreBulkRequestRequest $request, Resource $resource)
    {
        $bulkRequest = $this->bulkRequestService->create($request, $resource, $request->user());

        return $bulkRequest;
    }

    public function nursesBulkRequests(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $expirationStock = $this->bulkRequestService->getNursesBulkRequests($params, $request);
       
        $transformer = $this->bulkRequestService->getBulkRequestTransformer();

        return $this->datatablesService->datatableResponse($transformer, $expirationStock, $params);  
    }

    public function labBulkRequests(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $expirationStock = $this->bulkRequestService->getLabBulkRequests($params, $request);
       
        $transformer = $this->bulkRequestService->getBulkRequestTransformer();

        return $this->datatablesService->datatableResponse($transformer, $expirationStock, $params);  
    }

    public function pharmacyBulkRequests(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $expirationStock = $this->bulkRequestService->getPharmacyBulkRequests($params, $request);
       
        $transformer = $this->bulkRequestService->getBulkRequestTransformer();

        return $this->datatablesService->datatableResponse($transformer, $expirationStock, $params);  
    }

    public function theartreBulkRequests(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $expirationStock = $this->bulkRequestService->getTheartreBulkRequests($params, $request);
       
        $transformer = $this->bulkRequestService->getBulkRequestTransformer();

        return $this->datatablesService->datatableResponse($transformer, $expirationStock, $params);  
    }

    public function resolveThearterStock(Request $request, BulkRequest $bulkRequest, Resource $resource)
    {
        return $this->bulkRequestService->resolveTheartreStock($request, $bulkRequest, $resource, $request->user());
    }

    public function toggleApproveBulkRequest(UpdateBulkRequestRequest $request, BulkRequest $bulkRequest)
    {
        if ($request->user()->designation?->access_level < 5) {
            return response()->json(['message' => 'You are not authorized'], 403);
        }
        return $this->bulkRequestService->toggleRequest($request, $bulkRequest, $request->user());
    }

    public function dispenseBulkRequest(UpdateBulkRequestRequest $request, BulkRequest $bulkRequest)
    {
        return $this->bulkRequestService->dispenseRequest($request, $bulkRequest, $request->user());
    }

    public function destroy(Request $request, BulkRequest $bulkRequest)
    {
        if ($request->user()->designation?->access_level < 5) {
            return response()->json(['message' => 'You are not authorized'], 403);
        }
        return $this->bulkRequestService->processDeletion($bulkRequest);
    }

    public function destroyTheartre(Request $request, BulkRequest $bulkRequest)
    {
        if ($request->user()->designation?->access_level < 5) {
            return response()->json(['message' => 'You are not authorized'], 403);
        }
        return $this->bulkRequestService->processTheartreDeletion($bulkRequest);
    }
}
