<?php

namespace App\Http\Controllers;

use App\Enum\PayClass;
use App\Http\Resources\SponsorCategoryCollection;
use App\Http\Resources\SponsorCategoryResource;
use App\Models\SponsorCategory;
use App\Services\RequestService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use function Pest\Laravel\json;

class SponsorCategoryController extends Controller
{
    public function __construct(private readonly RequestService $requestService)
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
    public function store(Request $request)
    {
        $request->validate([
            'name'              => ['required', 'max:255', 'unique:'.SponsorCategory::class],
            'description'       => ['required', 'max:500'],
            'payClass'          => ['required', 'max:10'],
            'approval'          => ['required'],
            'billMatrix'        => ['required'],
            'balanceRequired'   => ['required'],
            'consultationFee'   => ['required']
        ]);

        $sponsorCategory = $request->user()->sponsorCategories()->create([
            'name' => $request->name,
            'description' => $request->description,
            'pay_class' => PayClass::from($request->payClass),
            'approval'  => filter_var($request->approval, FILTER_VALIDATE_BOOL),
            'bill_matrix' => $request->billMatrix,
            'balance_required' => filter_var($request->balanceRequired, FILTER_VALIDATE_BOOL),
            'consultation_fee' => $request->consultationFee
        ]);

        $sponsorCategory->load('user');

        return $sponsorCategory;
    }

    public function showAll(array $columns)
    {
        return SponsorCategory::all([$columns]);   
    }

    /**
     * Display all the resource.
     */
    public function load(SponsorCategory $sponsorCategory, Request $request)
    {
        // var_dump($request);
        $params = $this->requestService->getDataTableQueryParameters($request);

        $orderBy  =  'created_at';
        $orderDir =  'desc';
       
        $transformer = function (SponsorCategory $sponsorCategory) {
            return [
                'id'                => $sponsorCategory->id,
                'name'              => $sponsorCategory->name,
                'description'       => $sponsorCategory->description,
                'consultationFee'   => $sponsorCategory->consultation_fee,
                'payClass'          => $sponsorCategory->pay_class,
                'approval'          => $sponsorCategory->approval === 0 ? 'false' : 'true',
                'billMatrix'        => $sponsorCategory->bill_matrix,
                'balanceRequired'   => $sponsorCategory->balance_required === 0 ? 'false' : 'true',
                'createdAt'         => Carbon::parse($sponsorCategory->created_at)->format('d/m/Y')
            ];
         };
        
        $query = $sponsorCategory->orderBy($orderBy, $orderDir)->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

        if (! empty($params->searchTerm)) {
            $query= $sponsorCategory->where('name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
            ->orderBy($orderBy, $orderDir)->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }


        return response()->json([
            'data' => array_map($transformer, (array)$query->getIterator()),
            'draw' => $params->draw,
            'recordsTotal' => $query->total(),
            'recordsFiltered' => $query->total()
        ]);

        //return new SponsorCategoryCollection($query);

       
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
    public function update(Request $request, SponsorCategory $sponsorCategory)
    {
        $request->validate([
            'name'              => ['required', 'max:255', Rule::unique('sponsor_categories','name')->ignore($request->name, 'name')],
            'description'       => ['required', 'max:500'],
            'payClass'          => ['required', 'max:10'],
            'approval'          => ['required'],
            'billMatrix'        => ['required'],
            'balanceRequired'   => ['required'],
            'consultationFee'   => ['required']
        ]);
        
        $updated = $sponsorCategory->update([
            'name'              => $request->name,
            'description'       => $request->description,
            'pay_class'         => $request->payClass,
            'approval'          => filter_var($request->approval, FILTER_VALIDATE_BOOL),
            'bill_matrix'       => $request->billMatrix,
            'balance_required'  => filter_var($request->balanceRequired, FILTER_VALIDATE_BOOL),
            'consultation_fee'  =>  $request->consultationFee,
            'user_id'           => $request->user()->id

        ]);

        return $updated;
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