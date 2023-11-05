<?php

namespace App\Http\Controllers;

use App\Models\Resources;
use Illuminate\Http\Request;
use App\Services\DatatablesService;
use App\Http\Controllers\ResourceCategoryController;

class ResourcesController extends Controller
{
    public function __construct(
        private readonly ResourceCategoryController $resourceCategoryController, 
        private readonly DatatablesService $datatablesService, 
        // private readonly PatientService $patientService
        )
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('resources.resources',  ['categories' =>$this->resourceCategoryController->showAll('id', 'name')]);
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    // public function show(Resources $resources)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(Resources $resources)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(Request $request, Resources $resources)
    // {
    //     //
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(Resources $resources)
    // {
    //     //
    // }
}
