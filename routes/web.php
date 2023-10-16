<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HmoController;
use App\Http\Controllers\InvestigationsController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourcesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('Home');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    Route::get('/patients', [PatientController::class, 'index'])->name('Patients');
    Route::get('/doctors', [DoctorController::class, 'index'])->name('Doctors');
    Route::get('/nurses', [NurseController::class, 'index'])->name('Nurses');
    Route::get('/investigations', [InvestigationsController::class, 'index'])->name('Investigations');
    Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('Pharmacy');
    Route::get('/hmo', [HmoController::class, 'index'])->name('Hmo');
    Route::get('/billing', [BillingController::class, 'index'])->name('Billing');
    Route::get('/resources', [ResourcesController::class, 'index'])->name('Resources');
    Route::get('/admin', [AdminController::class, 'index'])->name('Admin');
});

require __DIR__.'/auth.php';
