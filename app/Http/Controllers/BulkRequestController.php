<?php

namespace App\Http\Controllers;

use App\Models\BulkRequest;
use App\Http\Requests\StoreBulkRequestRequest;
use App\Http\Requests\UpdateBulkRequestRequest;
use App\Models\Resource;
use App\Services\BulkRequestService;
use App\Services\DatatablesService;
use Illuminate\Http\Request;

class BulkRequestController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly BulkRequestService $bulkRequestService
    )
    {  
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BulkRequest $bulkRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBulkRequestRequest $request, BulkRequest $bulkRequest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BulkRequest $bulkRequest)
    {
        //
    }
}
