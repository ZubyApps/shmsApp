<?php

namespace App\Http\Controllers;

use App\Enum\PayClass;
use App\Models\SponsorCategory;
use Illuminate\Http\Request;

class SponsorCategoryController extends Controller
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

    /**
     * Display the specified resource.
     */
    public function show(SponsorCategory $sponsorCategory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SponsorCategory $sponsorCategory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SponsorCategory $sponsorCategory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SponsorCategory $sponsorCategory)
    {
        //
    }
}
