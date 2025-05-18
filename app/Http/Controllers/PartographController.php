<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePartographRequest;
use App\Http\Requests\UpdatePartographRequest;
use App\Models\Partograph;
use App\Services\DatatablesService;
use App\Services\PartographService;
use Illuminate\Http\Request;

class PartographController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly PartographService $partographService)
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePartographRequest $request)
    {
        // info(date('d-m-Y H:i:s'), [$request->recordedAt]);
        $partograph = $this->partographService->create($request, $request->user());
        
        return $partograph;
    }

    public function loadPartographTables(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $partograph = $this->partographService->getPartographData($params, $request);
       
        $loadTransformer = $this->partographService->getPartographDataTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $partograph, $params);  
    }

    public function loadPartographChartByLabourRecord(Request $request)
    {
        $partographs = $this->partographService->getPartographChartData($request);
        $loadTransformer = $this->partographService->getPartographDataTransformer();

        $outGoing = array_map($loadTransformer, (array)$partographs->getIterator());

        return response()->json($outGoing);
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Partograph $partograph)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePartographRequest $request, Partograph $partograph)
    {
        return $this->partographService->update($request, $partograph, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Partograph $partograph)
    {
        return $partograph->destroy($partograph->id);
    }
}
