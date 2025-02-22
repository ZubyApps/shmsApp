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
        try {
            $addResourceStock = $this->addResourceStockService->create($request, $request->user());
            return response()->json($addResourceStock, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to create resource stock'], 500);
        }
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
        try {
            $addResourceStock->resource()->update([
                'stock_level' => $addResourceStock->resource->stock_level - $addResourceStock->final_quantity
            ]);
            $addResourceStock->delete();
            return response()->json(['message' => 'Resource stock deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete resource stock'], 500);
        }
    }
}
