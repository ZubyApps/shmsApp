<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateResourceCategorySponsorRequest;
use App\Models\ResourceCategory;
use App\Models\ResourceCategorySponsor;
use App\Models\Sponsor;
use Illuminate\Http\Request;


class ResourceCategorySponsorController extends Controller
{

    public function storeResourceCatSponsorPercentage(Request $request, Sponsor $sponsor, ResourceCategory $resourceCategory)
    {
        $request->validate([
            'percentage' => 'required|numeric|min:0',
        ]);
    
        $user = auth()->user(); // The user setting the price
    
        $sponsor->resourceCategories()->syncWithoutDetaching([
            $resourceCategory->id => [
                'billable_percentage' => $request->percentage,
                'user_id' => $user->id,
            ]
        ]);

        return back()->with('success', 'Percentage rules updated successfully.');
    }

    public function removeSponsorResourceCatPercentage(Request $request, Sponsor $sponsor, ResourceCategory $resourceCategory)
    {        
            $sponsor->resourceCategories()->detach($resourceCategory->id);
    
            return response()->json(['message' => 'Percentage removed successfully']);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResourceCategorySponsor $resourceCategorySponsor)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateResourceCategorySponsorRequest $request, ResourceCategorySponsor $resourceCategorySponsor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceCategorySponsor $resourceCategorySponsor)
    {
        //
    }
}
