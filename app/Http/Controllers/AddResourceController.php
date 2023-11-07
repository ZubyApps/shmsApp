<?php

namespace App\Http\Controllers;

use App\Models\AddResource;
use App\Services\DatatablesService;
use App\Services\AddResourceStockService;
use App\Http\Requests\StoreAddResourceRequest;
use App\Http\Requests\UpdateAddResourceRequest;
use Illuminate\Http\Request;

class AddResourceController extends Controller
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
    public function store(StoreAddResourceRequest $request)
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
    public function show(AddResource $addResource)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AddResource $addResource)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAddResourceRequest $request, AddResource $addResource)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AddResource $addResource)
    {
        //
    }
}
