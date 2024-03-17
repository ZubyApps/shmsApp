<?php

namespace App\Http\Controllers;

use App\Enum\PayClass;
use App\Models\SponsorCategory;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly ExpenseCategoryController $expenseCategoryController
        )
    {
        
    }

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
    public function create(Request $request)
    {

    }

}
