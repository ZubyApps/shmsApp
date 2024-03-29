<?php

namespace App\Http\Controllers;

use App\Services\DatatablesService;
use App\Services\AddResourceStockService;
use App\Http\Requests\StoreAddResourceStockRequest;
use App\Models\AddResourceStock;
use Illuminate\Http\Request;

class AddResourceStockController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly AddResourceStockService $addResourceStockService,
        private readonly ResourceController $resourceController
        )
    {
        
    }
    
    public function store(StoreAddResourceStockRequest $request)
    {
        $addResourceStock = $this->addResourceStockService->create($request, $request->user());

        return $addResourceStock->load('user');
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->addResourceStockService->getPaginatedAddResourceStocks($params);
       
        $loadTransformer = $this->addResourceStockService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    public function destroy(AddResourceStock $addResourceStock)
    {
        $addResourceStock->resource()->update([
            'stock_level' => $addResourceStock->resource->stock_level - $addResourceStock->quantity
        ]);
        return $addResourceStock->destroy($addResourceStock->id);
    }
}
