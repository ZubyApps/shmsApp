<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use App\Models\ResourceSponsor;
use App\Models\Sponsor;
use Illuminate\Http\Request;

class ResourceSponsorController extends Controller
{
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


    public function storeSponsorSellingPrice(Request $request, Sponsor $sponsor, Resource $resource)
    {
            $request->validate([
                'sellingPrice' => 'required|numeric|min:0',
            ]);
    
            $user = auth()->user(); // The user setting the price
    
            $sponsor->resources()->syncWithoutDetaching([
                $resource->id => [
                    'selling_price' => $request->sellingPrice,
                    'user_id' => $user->id,
                ]
            ]);
    
            return response()->json(['message' => 'Tariff set successfully']);
    }

    public function removeSponsorSellingPrice(Request $request, Sponsor $sponsor, Resource $resource)
    {        
            $sponsor->resources()->detach($resource->id);
    
            return response()->json(['message' => 'Tariff removed successfully']);
    }

    /**
     * Display the specified resource.
     */
    public function show(ResourceSponsor $resourceSponsor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResourceSponsor $resourceSponsor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResourceSponsor $resourceSponsor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceSponsor $resourceSponsor)
    {
        //
    }
}
