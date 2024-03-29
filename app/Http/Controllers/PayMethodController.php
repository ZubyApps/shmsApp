<?php

namespace App\Http\Controllers;

use App\Models\PayMethod;
use App\Http\Requests\StorePayMethodRequest;
use App\Http\Requests\UpdatePayMethodRequest;
use App\Http\Resources\PayMethodResource;
use App\Services\DatatablesService;
use App\Services\PayMethodService;
use Illuminate\Http\Request;

class PayMethodController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly PayMethodService $payMethodService
        )
    {
        
    }

    public function store(StorePayMethodRequest $request)
    {
        return $this->payMethodService->create($request, $request->user());
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $payMethods = $this->payMethodService->getPaginatedPayMethods($params);
       
        $loadTransformer = $this->payMethodService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $payMethods, $params);  
    }

    public function edit(PayMethod $payMethod)
    {
        return new PayMethodResource($payMethod);
    }

    public function update(UpdatePayMethodRequest $request, PayMethod $payMethod)
    {
        return $this->payMethodService->update($request, $payMethod, $request->user());
    }

    public function destroy(PayMethod $payMethod)
    {
        return $payMethod->destroy($payMethod->id);
    }
}
