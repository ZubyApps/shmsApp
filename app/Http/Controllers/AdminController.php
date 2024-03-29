<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private readonly ExpenseCategoryController $expenseCategoryController
        )
    {
        
    }

    public function index()
    {
        return view('admin.admin');
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function create(Request $request)
    {

    }

}
