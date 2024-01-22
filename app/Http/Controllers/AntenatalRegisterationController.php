<?php

namespace App\Http\Controllers;

use App\Models\AntenatalRegisteration;
use App\Http\Requests\StoreAntenatalRegisterationRequest;
use App\Http\Requests\UpdateAntenatalRegisterationRequest;
use App\Http\Resources\AntenatalRegisterationResource;
use App\Services\AntenatalRegisterationService;
use App\Services\DatatablesService;

class AntenatalRegisterationController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly AntenatalRegisterationService $antenatalRegisterationService)
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
    public function store(StoreAntenatalRegisterationRequest $request)
    {
        $registeration = $this->antenatalRegisterationService->create($request, $request->user());
        
        return $registeration;
    }

    /**
     * Display the specified resource.
     */
    public function show(AntenatalRegisteration $antenatalRegisteration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AntenatalRegisteration $antenatalRegisteration)
    {
        return new AntenatalRegisterationResource($antenatalRegisteration);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAntenatalRegisterationRequest $request, AntenatalRegisteration $antenatalRegisteration)
    {
        return $this->antenatalRegisterationService->update($request, $antenatalRegisteration, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AntenatalRegisteration $antenatalRegisteration)
    {
        return $antenatalRegisteration->destroy($antenatalRegisteration->id);
    }
}
