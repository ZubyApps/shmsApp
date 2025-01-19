<?php

namespace App\Http\Controllers;

use App\Models\ThirdParty;
use App\Http\Requests\StoreThirdPartyRequest;
use App\Http\Requests\UpdateThirdPartyRequest;
use App\Http\Resources\ThirdPartyResource;
use App\Services\DatatablesService;
use App\Services\ThirdPartyServices;
use Illuminate\Http\Request;

class ThirdPartyController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly ThirdPartyServices $thirdPartyServices,
        )
    {
    }

    public function showAll(string ...$columns)
    {
        return ThirdParty::where('delisted', false)->get();
    }

    public function store(StoreThirdPartyRequest $request)
    {
        return $this->thirdPartyServices->create($request, $request->user());
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $thirdPartyServices = $this->thirdPartyServices->getPaginatedThirdParty($params);
       
        $loadTransformer = $this->thirdPartyServices->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $thirdPartyServices, $params);  
    }

    public function toggleDelisted(ThirdParty $thirdParty)
    {
        return $thirdParty->update([
            'delisted' => !$thirdParty->delisted
        ]);
    }

    public function edit(ThirdParty $thirdParty)
    {
        return new ThirdPartyResource($thirdParty);
    }

    public function update(UpdateThirdPartyRequest $request, ThirdParty $thirdParty)
    {
        return $this->thirdPartyServices->update($request, $thirdParty, $request->user());
    }

    public function destroy(ThirdParty $thirdParty)
    {
        return $thirdParty->destroy($thirdParty->id);
    }
}
