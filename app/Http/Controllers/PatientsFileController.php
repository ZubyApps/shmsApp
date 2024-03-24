<?php

namespace App\Http\Controllers;

use App\Models\PatientsFile;
use App\Http\Requests\StorePatientsFileRequest;
use App\Http\Requests\UpdatePatientsFileRequest;
use App\Models\Visit;
use App\Services\DatatablesService;
use App\Services\PatientsFileService;
use Illuminate\Http\Request;

class PatientsFileController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService,
        private readonly PatientsFileService $patientsFileService
        )
    {
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientsFileRequest $request, Visit $visit)
    {
        return $this->patientsFileService->create($request, $visit, $request->user());
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->patientsFileService->getPaginatedPatientsFile($params, $request);
       
        $loadTransformer = $this->patientsFileService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function download(PatientsFile $patientsFile)
    {
        return $this->patientsFileService->findFile($patientsFile);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientsFile $patientsFile)
    {
        return $this->patientsFileService->processDeletion($patientsFile);
    }
}
