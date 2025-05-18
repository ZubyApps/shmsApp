<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLabourRecordRequest;
use App\Http\Requests\UpdateLabourRecordRequest;
use App\Http\Resources\LabourRecordResource;
use App\Http\Resources\LabourSummaryResource;
use App\Models\LabourRecord;
use App\Services\DatatablesService;
use App\Services\LabourRecordService;
use Illuminate\Http\Request;

class LabourRecordController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly LabourRecordService $labourRecordService)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLabourRecordRequest $request)
    {
        $labourRecord = $this->labourRecordService->create($request, $request->user());
        
        return $labourRecord;
    }

    public function loadLabourRecordTable(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $labourRecords = $this->labourRecordService->getLabourRecords($params, $request);
       
        $loadTransformer = $this->labourRecordService->getLabourRecordTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $labourRecords, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LabourRecord $labourRecord)
    {
        return new LabourRecordResource($labourRecord);
    }

    public function editLabourSummary(LabourRecord $labourRecord)
    {
        return new LabourSummaryResource($labourRecord);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLabourRecordRequest $request, LabourRecord $labourRecord)
    {
        return $this->labourRecordService->update($request, $labourRecord, $request->user());
    }

    public function updateLabourSummary(UpdateLabourRecordRequest $request, LabourRecord $labourRecord)
    {
        return $this->labourRecordService->updateSummary($request, $labourRecord, $request->user());
    }

    public function labourInProgressDetails()
    {
        
        $laboursInProgress = $this->labourRecordService->inProgress();
        $loadTransformer = $this->labourRecordService->getLabourRecordTransformer();

        $outGoing = array_map($loadTransformer, (array)$laboursInProgress->getIterator());

        return response()->json($outGoing);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function deleteLabourSummary(LabourRecord $labourRecord)
    {
        return $this->labourRecordService->deleteSummary($labourRecord);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LabourRecord $labourRecord)
    {
        return $labourRecord->destroy($labourRecord->id);
    }
}
