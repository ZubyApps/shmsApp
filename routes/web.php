<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HmoController;
use App\Http\Controllers\InvestigationsController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\SponsorCategoryController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\VisitController;
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
    
    Route::get('/doctors', [DoctorController::class, 'index'])->name('Doctors');
    Route::get('/nurses', [NurseController::class, 'index'])->name('Nurses');
    Route::get('/investigations', [InvestigationsController::class, 'index'])->name('Investigations');
    Route::get('/pharmacy', [PharmacyController::class, 'index'])->name('Pharmacy');
    Route::get('/hmo', [HmoController::class, 'index'])->name('Hmo');
    Route::get('/billing', [BillingController::class, 'index'])->name('Billing');
    Route::get('/resources', [ResourcesController::class, 'index'])->name('Resources');
    Route::get('/admin', [AdminController::class, 'index'])->name('Admin');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('Settings');

    Route::prefix('sponsorcategory')->group(function () {
        Route::post('', [SponsorCategoryController::class, 'store']);
        Route::get('/load', [SponsorCategoryController::class, 'load']);
        Route::get('/list_sponsors/{sponsorCategory}', [SponsorCategoryController::class, 'list']);
        Route::get('/{sponsorCategory}', [SponsorCategoryController::class, 'edit']);
        Route::delete('/{sponsorCategory}', [SponsorCategoryController::class, 'destroy']);
        Route::post('/{sponsorCategory}', [SponsorCategoryController::class, 'update']);
    })->name('Sponsor Category');

    Route::prefix('sponsors')->group(function (){
        Route::post('', [SponsorController::class, 'store']);
        Route::get('/load', [SponsorController::class, 'load']);
        Route::get('/{sponsor}', [SponsorController::class, 'edit']);
        Route::delete('/{sponsor}', [SponsorController::class, 'destroy']);
        Route::post('/{sponsor}', [SponsorController::class, 'update']);
    })->name('Sponsors');

    Route::prefix('patients')->group(function () {
        Route::get('', [PatientController::class, 'index'])->name('Patients');
        Route::post('', [PatientController::class, 'store']);
        Route::get('/load', [PatientController::class, 'load']);
        Route::get('/{patient}', [PatientController::class, 'edit']);
        Route::delete('/{patient}', [PatientController::class, 'destroy']);
        Route::post('/{patient}', [PatientController::class, 'update']);
        Route::get('/initiate/{patient}', [PatientController::class, 'initiateVisit']);
        Route::post('/initiate/{patient}', [PatientController::class, 'confirmVisit']);
    })->name('Patients');

    Route::prefix('visits')->group(function () {
        Route::post('', [VisitController::class, 'store']);
        Route::get('/load', [VisitController::class, 'load']);
        Route::post('/consult/{visit}', [VisitController::class, 'consult']);
    })->name('Visits');

    Route::prefix('consultation')->group(function () {
        Route::post('', [ConsultationController::class, 'store']);
    });
    
});

require __DIR__.'/auth.php';
