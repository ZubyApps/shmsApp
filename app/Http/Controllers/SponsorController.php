<?php

namespace App\Http\Controllers;

use App\Models\Sponsor;
use App\Models\SponsorCategory;
use Illuminate\Http\Request;

class SponsorController extends Controller
{
    public function __construct(private readonly SponsorCategory $sponsorCategory)
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
            'name' => ['required', 'unique:'.Sponsor::class],
            'phone' => ['required', 'digits:11', 'unique:'.Sponsor::class],
            'email' => ['nullable', 'email', 'unique:'.Sponsor::class],
            'registrationBill' => ['nullable', 'numeric'],
            'category' => ['required']
        ]);

        $category = SponsorCategory::findOrFail($request->category);

        $sponsor = $request->user()->sponsors()->create([
            'name'                  => $request->name,
            'phone'                 => $request->phone,
            'email'                 => $request->email,
            'registration_bill'     => $request->registerBill,
            'sponsor_category_id'   => $category->id
        ]);

        return $sponsor->load('sponsorCategory');
    }

    public function list(Request $request, SponsorCategory $sponsorCategory)
    {   
        return $sponsorCategory->sponsors()->get(['id', 'name'])->toJson();
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sponsor $sponsor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sponsor $sponsor)
    {
        //
    }
}
