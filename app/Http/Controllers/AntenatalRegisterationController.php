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

    public function store(StoreAntenatalRegisterationRequest $request)
    {
        $registeration = $this->antenatalRegisterationService->create($request, $request->user());
        
        return $registeration;
    }

    public function edit(AntenatalRegisteration $antenatalRegisteration)
    {
        return new AntenatalRegisterationResource($antenatalRegisteration);
    }

    public function update(UpdateAntenatalRegisterationRequest $request, AntenatalRegisteration $antenatalRegisteration)
    {
        return $this->antenatalRegisterationService->update($request, $antenatalRegisteration, $request->user());
    }

    public function destroy(AntenatalRegisteration $antenatalRegisteration)
    {
        return $antenatalRegisteration->destroy($antenatalRegisteration->id);
    }
}
