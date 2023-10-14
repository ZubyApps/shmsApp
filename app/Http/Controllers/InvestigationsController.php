<?php

namespace App\Http\Controllers;

use App\Models\Investigations;
use Illuminate\Http\Request;

class InvestigationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('investigations.investigations');
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
    public function show(Investigations $investigations)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Investigations $investigations)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Investigations $investigations)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Investigations $investigations)
    {
        //
    }
}
