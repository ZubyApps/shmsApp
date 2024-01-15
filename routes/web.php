<?php

use App\Http\Controllers\AddResourceController;
use App\Http\Controllers\AddResourceStockController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HmoController;
use App\Http\Controllers\InvestigationController;
use App\Http\Controllers\MedicationChartController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResourceCategoryController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResourcesController;
use App\Http\Controllers\ResourceStockDateController;
use App\Http\Controllers\ResourceSubCategoryController;
use App\Http\Controllers\ResourceSupplierController;
use App\Http\Controllers\SponsorCategoryController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VitalSignsController;
use App\Models\AddResource;
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
    Route::get('/billing', [BillingController::class, 'index'])->name('Billing');
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
        Route::get('/list', [SponsorController::class, 'list']);
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
        Route::post('/initiate/{patient}', [PatientController::class, 'confirmVisit']);
        Route::patch('/knownclinicalinfo/{patient}', [PatientController::class, 'updateKnownClinicalInfo']);
    })->name('Patients');

    Route::prefix('visits')->group(function () {
        Route::post('', [VisitController::class, 'storeVisit']);
        Route::get('/load/waiting', [VisitController::class, 'loadWaitingTable']);
        Route::get('/load/consulted/', [VisitController::class, 'loadAllVisits']);
        Route::get('/load/consulted/inpatients', [VisitController::class, 'loadInpatientsVisits']);      
        Route::delete('/{visit}', [VisitController::class, 'destroy']);
    })->name('Visits');
    
    Route::prefix('doctors')->group(function () {
        Route::get('', [DoctorController::class, 'index'])->name('Doctors');
        Route::post('/consult/{visit}', [DoctorController::class, 'consult']);
        Route::get('/load/consulted/outpatient', [DoctorController::class, 'loadOutpatientVisits']);
        Route::get('/load/consulted/inpatient', [DoctorController::class, 'loadInpatientVisits']);
        Route::get('/load/consulted/anc', [DoctorController::class, 'loadAncPatientVisits']);
    });

    Route::prefix('nurses')->group(function () {
        Route::get('', [NurseController::class, 'index'])->name('Nurses');
        Route::get('/load/consulted/nurses', [NurseController::class, 'loadVisitsNurses']);
    });

    Route::prefix('consultation')->group(function () {
        Route::post('', [ConsultationController::class, 'store']);
        Route::post('/review', [ConsultationController::class, 'storeReview']);
        Route::post('/{consultation}', [ConsultationController::class, 'updateAdmissionStatus']);
        Route::get('/consultations/{visit}', [ConsultationController::class, 'loadConsultations']);
        Route::delete('/{consultation}', [ConsultationController::class, 'destroy']);
    });

    Route::prefix('vitalsigns')->group(function () {
        Route::post('', [VitalSignsController::class, 'store']);
        Route::get('/load/visit_vitalsigns', [VitalSignsController::class, 'loadVitalSignsTableByVisit']);
        Route::get('/load/visit_vitalsigns_chart', [VitalSignsController::class, 'loadVitalSignsChartByVisit']);
        Route::delete('/{vitalSigns}', [VitalSignsController::class, 'destroy']);
    });

    Route::prefix('resourcestockdate')->group(function (){
        Route::post('', [ResourceStockDateController::class, 'store']);
        Route::get('/load', [ResourceStockDateController::class, 'load']);
        Route::get('/{resourceStockDate}', [ResourceStockDateController::class, 'edit']);
        Route::post('/resetstock/{resourceStockDate}', [ResourceStockDateController::class, 'processReset']);
        Route::delete('/{resourceStockDate}', [ResourceStockDateController::class, 'destroy']);
        Route::post('/{resourceStockDate}', [ResourceStockDateController::class, 'update']);
    })->name('Stock Date');

    Route::prefix('resourcecategory')->group(function (){
        Route::post('', [ResourceCategoryController::class, 'store']);
        Route::get('/load', [ResourceCategoryController::class, 'load']);
        Route::get('/list_subcategories/{resourceCategory}', [ResourceCategoryController::class, 'list']);
        Route::get('/{resourceCategory}', [ResourceCategoryController::class, 'edit']);
        Route::delete('/{resourceCategory}', [ResourceCategoryController::class, 'destroy']);
        Route::post('/{resourceCategory}', [ResourceCategoryController::class, 'update']);
    })->name('Resource Category');

    Route::prefix('resourcesubcategory')->group(function (){
        Route::post('', [ResourceSubCategoryController::class, 'store']);
        Route::get('/load', [ResourceSubCategoryController::class, 'load']);
        Route::get('/{resourceSubCategory}', [ResourceSubCategoryController::class, 'edit']);
        Route::delete('/{resourceSubCategory}', [ResourceSubCategoryController::class, 'destroy']);
        Route::post('/{resourceSubCategory}', [ResourceSubCategoryController::class, 'update']);
    })->name('Resource SubCategory');

    Route::prefix('resources')->group(function (){
        Route::get('', [ResourceController::class, 'index'])->name('Resources');
        Route::post('', [ResourceController::class, 'store']);
        Route::get('/load', [ResourceController::class, 'load']);
        Route::get('/list', [ResourceController::class, 'list']);
        Route::get('/{resource}', [ResourceController::class, 'edit']);
        Route::get('/addstock/{resource}', [ResourceController::class, 'edit'])->name('Addstock');
        Route::delete('/{resource}', [ResourceController::class, 'destroy']);
        Route::post('/{resource}', [ResourceController::class, 'update']);
        Route::post('toggle/{resource}', [ResourceController::class, 'toggleIsActive']);
    })->name('Resources');
    
    Route::prefix('addresourcestock')->group(function (){
        Route::post('', [AddResourceStockController::class, 'store']);
        Route::get('/load', [AddResourceStockController::class, 'load']);
        Route::get('/{addResourceStock}', [AddResourceStockController::class, 'edit']);
        Route::delete('/{addResourceStock}', [AddResourceStockController::class, 'destroy']);
        Route::post('/{addResourceStock}', [AddResourceStockController::class, 'update']);
    })->name('AddStock');

    Route::prefix('resourcesupplier')->group(function (){
        Route::post('', [ResourceSupplierController::class, 'store']);
        Route::get('/load', [ResourceSupplierController::class, 'load']);
        Route::get('/list', [ResourceSupplierController::class, 'list']);
        Route::get('/{resourceSupplier}', [ResourceSupplierController::class, 'edit']);
        Route::delete('/{resourceSupplier}', [ResourceSupplierController::class, 'destroy']);
        Route::post('/{resourceSupplier}', [ResourceSupplierController::class, 'update']);
    })->name('Resource Supplier');

    Route::prefix('prescription')->group(function (){
        Route::get('', [PrescriptionController::class, 'index'])->name('Prescription');
        Route::post('', [PrescriptionController::class, 'store']);
        Route::get('/load/initial', [PrescriptionController::class, 'loadInitialTable']);
        Route::get('/load/lab', [PrescriptionController::class, 'loadLabTable']);
        Route::get('/load/treatment', [PrescriptionController::class, 'loadTreatmentTable']);
        Route::get('/list', [PrescriptionController::class, 'list']);
        Route::delete('/{prescription}', [PrescriptionController::class, 'destroy']);
    })->name('Prescription');

    Route::prefix('medicationchart')->group(function (){
        Route::get('', [MedicationChartController::class, 'index'])->name('MedicationChart');
        Route::post('', [MedicationChartController::class, 'store']);
        Route::get('/load/chart', [MedicationChartController::class, 'loadMedicationChartByPrescription']);
        Route::get('/load/upcoming', [MedicationChartController::class, 'loadUpcomingMedications']);
        Route::get('/load/treatment', [MedicationChartController::class, 'loadTreatmentTable']);
        Route::get('/list', [MedicationChartController::class, 'list']);
        Route::get('/{medicationChart}', [MedicationChartController::class, 'edit']);
        Route::delete('/{medicationChart}', [MedicationChartController::class, 'destroy']);
        Route::patch('/removegiven/{medicationChart}', [MedicationChartController::class, 'removeGivenData']);
        Route::patch('/{medicationChart}', [MedicationChartController::class, 'saveGivenData']);
    })->name('MedicationChart');

    Route::prefix('investigations')->group(function () {
        Route::get('', [InvestigationController::class, 'index'])->name('Investigations');
        Route::get('/load/consulted', [InvestigationController::class, 'loadVisitsByFilterLab']);
        Route::get('/load/inpatients', [InvestigationController::class, 'loadInpatientsLabTable']);
        Route::get('/{prescription}', [InvestigationController::class, 'edit']);
        Route::patch('/remove/{prescription}', [InvestigationController::class, 'removeLabResult']);
        Route::patch('/{prescription}', [InvestigationController::class, 'saveLabResult']);
    });

    Route::prefix('hmo')->group(function () {
        Route::get('', [HmoController::class, 'index'])->name('Hmo');
        Route::post('/verify/{visit}', [HmoController::class, 'verifyPatient']);
        Route::get('/load/consulted/', [HmoController::class, 'loadVisitsByFilterHmo']);
        Route::get('/load/verification/list', [HmoController::class, 'loadVerificationListTable']);
        Route::get('/load/approval/list', [HmoController::class, 'loadHmoApprovalListTable']);
        Route::patch('/approve/{prescription}', [HmoController::class, 'approveItem']);
        Route::patch('/reject/{prescription}', [HmoController::class, 'rejectItem']);
        Route::patch('/reset/{prescription}', [HmoController::class, 'resetItem']);
        Route::get('/load/visit/prescriptions', [HmoController::class, 'loadVisitPrescriptions']);
        Route::patch('/bill/{prescription}', [HmoController::class, 'saveHmoBill']);
    });

    Route::prefix('pharmacy')->group(function () {
        Route::get('', [PharmacyController::class, 'index'])->name('Pharmacy');
        Route::get('/load/consulted', [PharmacyController::class, 'loadVisitsByFilterPharmacy']);
        Route::patch('/bill/{prescription}', [PharmacyController::class, 'billPrescription']);
        Route::patch('/dispense/{prescription}', [PharmacyController::class, 'dispensePrescription']);
        Route::patch('/dispense/comment/{prescription}', [PharmacyController::class, 'dispenseComment']);
        Route::get('/load/visit/prescriptions', [PharmacyController::class, 'loadVisitPrescriptions']);
        Route::get('/load/consultation/prescriptions', [PharmacyController::class, 'loadConsultationPrescriptions']);
    });

    Route::prefix('billing')->group(function () {
        Route::get('', [BillingController::class, 'index'])->name('Billing');
        Route::get('/load/consulted', [BillingController::class, 'loadVisitsByFilterBilling']);
        Route::get('load/bill', [BillingController::class, 'loadPatientBillTable']);
        Route::get('load/payment', [BillingController::class, 'loadPatientPaymentTable']);
        Route::post('/pay', [BillingController::class, 'store']);
        Route::patch('/discount/{visit}', [BillingController::class, 'saveDiscount']);
        Route::delete('/payment/delete/{payment}', [BillingController::class, 'destroy']);
        Route::get('/load/outstandings', [BillingController::class, 'loadVisitsWithOutstandingBills']);
    });

    Route::prefix('deliverynote')->group(function () {
        Route::post('', [DeliveryNoteController::class, 'store']);
        Route::get('load/details', [DeliveryNoteController::class, 'loadDeliveryNoteTable']);
        Route::get('/{deliveryNote}', [DeliveryNoteController::class, 'edit']);
        Route::patch('/{deliveryNote}', [DeliveryNoteController::class, 'update']);
        Route::delete('', [DeliveryNoteController::class, 'destroy']);
    });
});

require __DIR__.'/auth.php';
