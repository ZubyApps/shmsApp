<?php

namespace App\Http\Controllers;

use App\Enum\PayClass;
use App\Http\Requests\StoreSponsorCategoryRequest;
use App\Http\Requests\UpdateSponsorCategoryRequest;
use App\Http\Resources\SponsorCategoryCollection;
use App\Http\Resources\SponsorCategoryResource;
use App\Models\SponsorCategory;
use App\Services\DatatablesService;
use App\Services\RequestService;
use App\Services\SponsorCategoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use function Pest\Laravel\json;

class SponsorCategoryController extends Controller
{
    public function __construct(
        private readonly DatatablesService $datatablesService, 
        private readonly SponsorCategoryService $sponsorCategoryService)
    {
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSponsorCategoryRequest $request)
    {
        $sponsorCategory = $this->sponsorCategoryService->create($request, $request->user());

        return $sponsorCategory;
    }

    public function showAll(string ...$columns)
    {
        return SponsorCategory::all($columns);   
    }

    /**
     * Display all the resource.
     */
    public function load(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsorCategories = $this->sponsorCategoryService->getPaginatedSponsorCategories($params);
       
        $loadTransformer = $this->sponsorCategoryService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $sponsorCategories, $params);  
    }
    

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SponsorCategory $sponsorCategory)
    {
        return new SponsorCategoryResource($sponsorCategory);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSponsorCategoryRequest $request, SponsorCategory $sponsorCategory)
    {        
        return $this->sponsorCategoryService->update($request, $sponsorCategory, $request->user());

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SponsorCategory $sponsorCategory)
    {
        return $sponsorCategory->destroy($sponsorCategory->id);
    }
}


// $transformer = function (SponsorCategory $sponsorCategory) {
        //     return [
        //         'id'                => $sponsorCategory->id,
        //         'name'              => $sponsorCategory->name,
        //         'description'       => $sponsorCategory->description,
        //         'consultationFee'   => $sponsorCategory->consultation_fee,
        //         'payClass'          => $sponsorCategory->pay_class,
        //         'approval'          => $sponsorCategory->approval === 0 ? 'false' : 'true',
        //         'billMatrix'        => $sponsorCategory->bill_matrix,
        //         'balanceRequired'   => $sponsorCategory->balance_required === 0 ? 'false' : 'true',
        //         'createdAt'         => Carbon::parse($sponsorCategory->created_at)->format('d/m/Y')
        //     ];
        //  };

         // return response()->json([
        //     'data' => array_map($transformer, (array)$query->getIterator()),
        //     'draw' => $params->draw,
        //     'recordsTotal' => $sponsorCategory::count(),
        //     'recordsFiltered' => $totalSponsors
        // ]);