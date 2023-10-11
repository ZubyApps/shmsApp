<?php

namespace App\Http\Controllers;

use App\Models\Hmo;
use Illuminate\Http\Request;

class HmoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('hmo.hmodesk');
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
    public function show(Hmo $hmo)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Hmo $hmo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Hmo $hmo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Hmo $hmo)
    {
        //
    }
}
