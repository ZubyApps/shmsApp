<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSponsorRequest;
use App\Http\Requests\UpdateSponsorRequest;
use App\Http\Resources\SponsorResource;
use App\Models\Sponsor;
use App\Services\DatatablesService;
use App\Services\SponsorService;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly SponsorService $sponsorService)
    {
        
    }

    public function list(Request $request, Sponsor $sponsor)
    {   
        return $sponsor::where('category', $request->category)->orderBy('name')->get(['id', 'name'])->toJson();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSponsorRequest $request)
    {
        return $this->sponsorService->create($request, $request->user());
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->sponsorService->getPaginatedSponsors($params);
       
        $loadTransformer = $this->sponsorService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sponsor $sponsor)
    {
        return new SponsorResource($sponsor);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSponsorRequest $request, Sponsor $sponsor)
    {
        return $this->sponsorService->update($request, $sponsor, $request->user());
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sponsor $sponsor)
    {
        return $sponsor->destroy($sponsor->id);
    }
}
