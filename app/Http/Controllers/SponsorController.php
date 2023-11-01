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
    public function store(StoreSponsorRequest $request)
    {
        $sponsor = $this->sponsorService->create($request, $request->user());

        return $sponsor->load('sponsorCategory');
    }

    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->sponsorService->getPaginatedSponsors($params);
       
        $loadTransformer = $this->sponsorService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsors, $params);  
    }

    /**
     * Display the specified resource.
     */
    public function show(Sponsor $sponsor)
    {
        //
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
