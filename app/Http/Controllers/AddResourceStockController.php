<?php

namespace App\Http\Controllers;

use App\Services\DatatablesService;
use App\Services\AddResourceStockService;
use App\Http\Requests\StoreAddResourceStockRequest;
use App\Http\Requests\UpdateAddResourceStockRequest;
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
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

    /**
     * Display the specified resource.
     */
    public function show(AddResourceStock $addResourceStock)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AddResourceStock $addResourceStock)
    {
        $addResourceStock->resource()->update([
            'stock_level' => $addResourceStock->resource->stock_level - $addResourceStock->quantity
        ]);
        return $addResourceStock->destroy($addResourceStock->id);
    }
}
