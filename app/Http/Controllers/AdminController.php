<?php

namespace App\Http\Controllers;

use App\Enum\PayClass;
use App\Models\SponsorCategory;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.admin');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function createSponsor(Request $request)
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

        $sponsorCategory = $request->user()->sponsorCategory()->create([
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

}
