<?php

use App\Http\Controllers\AddResourceStockController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AncVitalSignsController;
use App\Http\Controllers\AntenatalRegisterationController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\BulkRequestController;
use App\Http\Controllers\CapitationPaymentController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\HmoController;
use App\Http\Controllers\InvestigationController;
use App\Http\Controllers\MedicalReportController;
use App\Http\Controllers\MedicationCategoryController;
use App\Http\Controllers\MedicationChartController;
use App\Http\Controllers\NurseController;
use App\Http\Controllers\NursesReportController;
use App\Http\Controllers\NursingChartController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientsFileController;
use App\Http\Controllers\PayMethodController;
use App\Http\Controllers\PharmacyController;
use App\Http\Controllers\PrescriptionController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceCategoryController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\ResourceStockDateController;
use App\Http\Controllers\ResourceSubCategoryController;
use App\Http\Controllers\ResourceSupplierController;
use App\Http\Controllers\ShiftPerformanceController;
use App\Http\Controllers\ShiftReportController;
use App\Http\Controllers\SponsorCategoryController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\SurgeryNoteController;
use App\Http\Controllers\ThirdPartyController;
use App\Http\Controllers\ThirdPartyServiceController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\VitalSignsController;
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
    Route::middleware('strict')->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('Admin');
        Route::get('/admin/settings', [AdminController::class, 'settings'])->name('Settings');

        Route::prefix('users')->group(function () {
            Route::get('', [RegisteredUserController::class, 'create'])->name('users');
            Route::post('', [RegisteredUserController::class, 'store'])->name('register');
            Route::get('/allstaff', [RegisteredUserController::class, 'loadAllUsers']);
            Route::get('/activestaff', [RegisteredUserController::class, 'loadActiveUsers']);
            Route::get('/{user}', [RegisteredUserController::class, 'edit']);
            Route::get('designation/{user}', [RegisteredUserController::class, 'designation']);
            Route::delete('/{user}', [RegisteredUserController::class, 'destroy']);
            Route::post('/logout/{user}', [RegisteredUserController::class, 'logStaffOut']);
            Route::delete('/designate/{designation}', [RegisteredUserController::class, 'removeDesignation']);
            Route::patch('/{user}', [RegisteredUserController::class, 'update']);
            Route::post('/designate/{user}', [RegisteredUserController::class, 'assignDesignation']);
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
            Route::get('/list/bulk', [ResourceController::class, 'listBulk']);
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
    
        Route::prefix('paymethod')->group(function (){
            Route::post('', [PayMethodController::class, 'store']);
            Route::get('/load', [PayMethodController::class, 'load']);
            Route::get('/methods', [PayMethodController::class, 'list']);
            Route::get('/{payMethod}', [PayMethodController::class, 'edit']);
            Route::delete('/{payMethod}', [PayMethodController::class, 'destroy']);
            Route::patch('/{payMethod}', [PayMethodController::class, 'update']);
        })->name('Pay Methods');

        Route::prefix('expensecategory')->group(function (){
            Route::post('', [ExpenseCategoryController::class, 'store']);
            Route::get('/load', [ExpenseCategoryController::class, 'loadExpenseCategories']);
            Route::get('/listcategories/{expenseCategory}', [ExpenseCategoryController::class, 'list']);
            Route::get('/{expenseCategory}', [ExpenseCategoryController::class, 'edit']);
            Route::delete('/{expenseCategory}', [ExpenseCategoryController::class, 'destroy']);
            Route::post('/{expenseCategory}', [ExpenseCategoryController::class, 'update']);
        })->name('Expense Category');

        Route::prefix('reports')->group(function (){
            Route::get('', [ReportController::class, 'index'])->name('Reports');
            Route::get('/patients', [ReportController::class, 'indexPatients'])->name('Patients Reports');
            Route::get('/patients/dist1', [ReportController::class, 'loadPatientsDistribution1']);
            Route::get('/patients/dist2', [ReportController::class, 'loadPatientsDistribution2']);
            Route::get('/patients/bysponsor', [ReportController::class, 'loadBySponsor']);
            Route::get('/patients/bysponsormonth', [ReportController::class, 'loadBySponsorMonth']);
            Route::get('/patients/frequency', [ReportController::class, 'loadPatientFrequency']);
            Route::get('/patients/regsummary', [ReportController::class, 'loadRegSummary']);
            Route::get('/medservices', [ReportController::class, 'indexMedServices'])->name('Medical Services');
            Route::get('/medservices/summary', [ReportController::class, 'loadMedServicesSummary']);
            Route::get('/medservices/byresource', [ReportController::class, 'loadByResource']);
            Route::get('/medservices/newbirths', [ReportController::class, 'loadNewBirths']);
            Route::get('/medservices/bydischarge', [ReportController::class, 'loadVisitsByDischarge']);
            Route::get('/medservices/dischargesummary', [ReportController::class, 'loadDischargeSummary']);
            Route::get('/investigations', [ReportController::class, 'indexInvestigations'])->name('Investigation Reports');
            Route::get('/investigations/summary', [ReportController::class, 'loadInvestigationsSummary']);
            Route::get('/investigations/byresource', [ReportController::class, 'loadByResource']);
            Route::get('/pharmacy', [ReportController::class, 'indexPharmacy'])->name('Pharmacy Reports');
            Route::get('/pharmacy/summary', [ReportController::class, 'loadPharmacySummary']);
            Route::get('/pharmacy/byresource', [ReportController::class, 'loadByResourcePharmacy']);
            Route::get('/pharmacy/missing', [ReportController::class, 'loadMissingPharmacySummary']);
            Route::get('/hospitalandothers', [ReportController::class, 'indexHospitalAndOthers'])->name('Hospital Services Reports');
            Route::get('/hospitalandothers/summary', [ReportController::class, 'loadHospitalAndOthersSummary']);
            Route::get('/hospitalandothers/byresource', [ReportController::class, 'loadByResource']);
            Route::get('/resources', [ReportController::class, 'indexResources'])->name('Resource Reports');
            Route::get('/resources/summary', [ReportController::class, 'loadResourceValueSummary']);
            Route::get('/resources/usedsummary', [ReportController::class, 'loadUsedResourcesSummary']);
            Route::get('/resources/bycategoryresource', [ReportController::class, 'loadByCategoryResource']);
            Route::get('/resources/expiratonstock', [ReportController::class, 'loadByExpirationOrStock']);
            Route::get('/accounts', [ReportController::class, 'indexAccounts'])->name('Account Reports');
            Route::get('/accounts/paymethodsummary', [ReportController::class, 'loadPayMethodsSummary']);
            Route::get('/accounts/capitation', [ReportController::class, 'loadCapitationPayments']);
            Route::get('/accounts/tpsssummary', [ReportController::class, 'loadTPSSummary']);
            Route::get('/accounts/tpsbythirdparty', [ReportController::class, 'loadTPSByThirdParty']);
            Route::get('/accounts/expensesummary', [ReportController::class, 'loadExpensesSummary']);
            Route::get('/accounts/visitsummary1', [ReportController::class, 'loadVisitsSummaryBySponsorCategory']);
            Route::get('/accounts/visitsummary2', [ReportController::class, 'loadVisitsSummaryBySponsor']);
            Route::get('/accounts/bypaymethod', [ReportController::class, 'loadPaymentsByPayMethod']);
            Route::get('/accounts/byvisitbysponsor', [ReportController::class, 'loadVisitsBySponsor']);
            Route::get('/accounts/yearlysummary', [ReportController::class, 'loadYearlyIncomeAndExpense']);
            Route::get('/users', [ReportController::class, 'indexUsers'])->name('Users Reports');
            Route::get('/users/doctors', [ReportController::class, 'loadDoctorsActivity']);
            Route::get('/users/nurses', [ReportController::class, 'loadNursesActivity']);
            Route::get('/users/labtechs', [ReportController::class, 'loadLabTechsActivity']);
            Route::get('/users/pharmacytechs', [ReportController::class, 'loadPharmacyTechsActivity']);
            Route::get('/users/hmoofficers', [ReportController::class, 'loadHmoOfficersActivity']);
            Route::get('/users/billofficers', [ReportController::class, 'loadBillOfficersActivity']);
        });
    });

    Route::middleware('doctor')->group(function () {
        Route::prefix('doctors')->group(function () {
            Route::get('/list', [DoctorController::class, 'list']);
            Route::get('', [DoctorController::class, 'index'])->name('Doctors');
            Route::post('/consult/{visit}', [DoctorController::class, 'consult']);
            Route::post('/review/{visit}', [DoctorController::class, 'review']);
            Route::get('/load/consulted/outpatient', [DoctorController::class, 'loadOutpatientVisits']);
            Route::get('/load/consulted/inpatient', [DoctorController::class, 'loadInpatientVisits']);
            Route::get('/load/consulted/anc', [DoctorController::class, 'loadAncPatientVisits']);
        });
    });

    Route::middleware('nurse')->group(function () {
        Route::prefix('nurses')->group(function () {
            Route::get('', [NurseController::class, 'index'])->name('Nurses');
            Route::get('/load/consulted/nurses', [NurseController::class, 'loadVisitsNurses']);
            Route::get('/list/emergency', [NurseController::class, 'emergencyList']);
            Route::patch('/done/{visit}', [NurseController::class, 'nurseDone']);
        });
    });

    Route::middleware('lab')->group(function () {
        Route::prefix('investigations')->group(function () {
            Route::get('', [InvestigationController::class, 'index'])->name('Investigations');
            Route::get('/load/consulted', [InvestigationController::class, 'loadVisitsByFilterLab']);
            Route::get('/load/inpatients', [InvestigationController::class, 'loadInpatientsLabTable']);
            Route::get('/load/outpatients', [InvestigationController::class, 'loadOutpatientsLabTable'])->withoutMiddleware('lab');
            Route::get('/{prescription}', [InvestigationController::class, 'edit']);
            Route::patch('/remove/{prescription}', [InvestigationController::class, 'removeLabResult']);
            Route::patch('/create/{prescription}', [InvestigationController::class, 'createLabResult']);
            Route::patch('/update/{prescription}', [InvestigationController::class, 'updateLabResult']);
            Route::patch('/removalreason/{prescription}', [InvestigationController::class, 'removeLabTest']);
            Route::get('/printall/{prescription}', [InvestigationController::class, 'getAllTestsAndResults']);
        });
    });

    Route::middleware('pharmacy')->group(function () {
        Route::prefix('pharmacy')->group(function () {
            Route::get('', [PharmacyController::class, 'index'])->name('Pharmacy');
            Route::get('/load/consulted', [PharmacyController::class, 'loadVisitsByFilterPharmacy']);
            Route::patch('/bill/{prescription}', [PharmacyController::class, 'billPrescription']);
            Route::patch('/dispense/{prescription}', [PharmacyController::class, 'dispensePrescription']);
            Route::patch('/hold/{prescription}', [PharmacyController::class, 'holdPrescription']);
            Route::patch('/dispense/comment/{prescription}', [PharmacyController::class, 'dispenseComment']);
            Route::get('/load/visit/prescriptions', [PharmacyController::class, 'loadVisitPrescriptions']);
            Route::get('/load/consultation/prescriptions', [PharmacyController::class, 'loadConsultationPrescriptions']);
            Route::get('/load/expiratonstock', [PharmacyController::class, 'expirationStock']);
            Route::get('/load/bulkrequests/nurses', [PharmacyController::class, 'expirationStock']);
            Route::get('/load/bulkrequests/lab', [PharmacyController::class, 'expirationStock']);
            Route::get('/load/bulkrequests/pharmacy', [PharmacyController::class, 'expirationStock']);
            Route::patch('/done/{visit}', [PharmacyController::class, 'pharmacyDone']);
        });
    });

    Route::middleware('hmo')->group(function () {
        Route::prefix('hmo')->group(function () {
            Route::get('', [HmoController::class, 'index'])->name('Hmo');
            Route::post('/verify/{visit}', [HmoController::class, 'verifyPatient']);
            Route::get('/consulted', [HmoController::class, 'loadVisitsByFilterHmo']);
            Route::get('/verification/list', [HmoController::class, 'loadVerificationListTable']);
            Route::get('/approval/list', [HmoController::class, 'loadHmoApprovalListTable']);
            Route::patch('/approve/{prescription}', [HmoController::class, 'approveItem']);
            Route::patch('/reject/{prescription}', [HmoController::class, 'rejectItem']);
            Route::patch('/reset/{prescription}', [HmoController::class, 'resetItem']);
            Route::get('/visit/prescriptions', [HmoController::class, 'loadVisitPrescriptions']);
            Route::patch('/bill/{prescription}', [HmoController::class, 'saveHmoBill']);
            Route::patch('/treat/{visit}', [HmoController::class, 'treatVisit']);
            Route::patch('/markassent/{visit}', [HmoController::class, 'markBillAsSent']);
            Route::get('/sentbills', [HmoController::class, 'sentBillsTable']);
            Route::get('/summary', [HmoController::class, 'loadReportSummary']);
            Route::get('/reconciliation', [HmoController::class, 'loadReconciliationTable']);
            Route::patch('/pay/{prescription}', [HmoController::class, 'reconciliationPayments']);
            Route::get('/capitation', [HmoController::class, 'loadCapitationReconciliation']);
    
        });

        Route::prefix('capitation')->group(function () {
            Route::post('', [CapitationPaymentController::class, 'store'])->name('Capitation');
            Route::delete('/{capitationPayment}', [CapitationPaymentController::class, 'destroy']);
        });
    });

    Route::middleware('billing')->group(function () {
        Route::prefix('billing')->group(function () {
            Route::get('', [BillingController::class, 'index'])->name('Billing');
            Route::get('/load/consulted', [BillingController::class, 'loadVisitsByFilterBilling']);
            Route::get('/load/openvisits', [BillingController::class, 'loadAllOpenVisits']);
            Route::get('/bill', [BillingController::class, 'loadPatientBill'])->withoutMiddleware('billing');
            Route::get('/payment', [BillingController::class, 'loadPatientPayment']);
            Route::get('/summary', [BillingController::class, 'loadBillSummary']);
            Route::post('/pay', [BillingController::class, 'store']);
            Route::patch('/discount/{visit}', [BillingController::class, 'saveDiscount']);
            Route::delete('/payment/delete/{payment}', [BillingController::class, 'destroy']);
            Route::get('/load/outstandings', [BillingController::class, 'loadVisitsWithOutstandingBills']);
            Route::get('/load/expenses', [BillingController::class, 'loadExpenses']);
            Route::get('/load/balancing', [BillingController::class, 'loadCashPaymentsAndExpenses']);
            Route::post('/dischargebill', [BillingController::class, 'addDischargeBill']);

        });

        Route::prefix('expenses')->group(function () {
            Route::post('', [ExpenseController::class, 'store'])->name('Expenses');
            Route::get('/{expense}', [ExpenseController::class, 'edit']);
            Route::delete('/{expense}', [ExpenseController::class, 'destroy']);
            Route::post('/{expense}', [ExpenseController::class, 'update']);
        });
    });

    Route::middleware('patients')->group(function () {
        Route::prefix('patients')->group(function () {
            Route::get('', [PatientController::class, 'index'])->name('Patients');
            Route::post('', [PatientController::class, 'store']);
            Route::get('/load', [PatientController::class, 'load']);
            Route::get('/load/summary/sponsor', [PatientController::class, 'loadRegSummaryBySponsor']);
            Route::get('/load/summary/sex', [PatientController::class, 'loadSummaryBySex']);
            Route::get('/load/summary/age', [PatientController::class, 'loadSummaryByAge']);
            Route::get('/load/summary/visits', [PatientController::class, 'loadVisitSummaryBySponsor']);
            Route::get('/load/bysponsor', [PatientController::class, 'loadPatientsBySponsor']);
            Route::get('/load/visits', [PatientController::class, 'loadVisit']);
            Route::get('/{patient}', [PatientController::class, 'edit']);
            Route::delete('/{patient}', [PatientController::class, 'destroy']);
            Route::post('/{patient}', [PatientController::class, 'update']);
            Route::post('/initiate/{patient}', [PatientController::class, 'confirmVisit']);
            Route::patch('/knownclinicalinfo/{patient}', [PatientController::class, 'updateKnownClinicalInfo'])->withoutMiddleware('patients');
        })->name('Patients');
    });

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

    Route::prefix('visits')->group(function () {
        Route::post('/{patient}', [VisitController::class, 'storeVisit']);
        Route::patch('changesponsor/{visit}', [VisitController::class, 'changeSponsor']);
        Route::get('/load/waiting', [VisitController::class, 'loadWaitingTable']);
        Route::get('/load/consulted/', [VisitController::class, 'loadAllVisits']);
        Route::get('/load/consulted/inpatients', [VisitController::class, 'loadInpatientsVisits']);      
        Route::patch('discharge/{visit}', [VisitController::class, 'dischargePatient']);
        Route::patch('/close/{visit}', [VisitController::class, 'closeVisit']);
        Route::patch('/delete/{visit}', [VisitController::class, 'destroy']);
        Route::patch('/open/{visit}', [VisitController::class, 'openVisit']);
        Route::get('/average', [VisitController::class, 'getPatientAverageWaitingTime']);
        Route::patch('/review/{visit}', [VisitController::class, 'reviewVisit']);
        Route::patch('/resolve/{visit}', [VisitController::class, 'resolveVisit']);
        Route::delete('/{visit}', [VisitController::class, 'destroy']);
    })->name('Visits');

    Route::prefix('consultation')->group(function () {
        Route::post('', [ConsultationController::class, 'store']);
        Route::post('/review', [ConsultationController::class, 'storeReview']);
        Route::post('/{consultation}', [ConsultationController::class, 'update']);
        Route::post('/review/{consultation}', [ConsultationController::class, 'update']);
        Route::patch('updatestatus/{consultation}', [ConsultationController::class, 'updateAdmissionStatus']);
        Route::get('/consultations/{visit}', [ConsultationController::class, 'loadConsultations']);
        Route::get('/history/{patient}', [ConsultationController::class, 'loadVisitsAndConsultations']);
        Route::delete('/{consultation}', [ConsultationController::class, 'destroy']);
    });

    Route::prefix('vitalsigns')->group(function () {
        Route::post('', [VitalSignsController::class, 'store']);
        Route::get('/load/table', [VitalSignsController::class, 'loadVitalSignsTableByVisit']);
        Route::get('/load/chart', [VitalSignsController::class, 'loadVitalSignsChartByVisit']);
        Route::delete('/{vitalSigns}', [VitalSignsController::class, 'destroy']);
    });
    
    Route::prefix('prescription')->group(function (){
        Route::get('', [PrescriptionController::class, 'index'])->name('Prescription');
        Route::post('{resource}', [PrescriptionController::class, 'store']);
        Route::get('/load/initial', [PrescriptionController::class, 'loadInitialTable']);
        Route::get('/load/emergency', [PrescriptionController::class, 'loadEmergencyTable']);
        Route::get('/load/lab', [PrescriptionController::class, 'loadLabTable']);
        Route::get('/load/medications', [PrescriptionController::class, 'loadMedicationTable']);
        Route::get('/load/others', [PrescriptionController::class, 'loadOtherPrescriptionsTable']);
        Route::get('/list', [PrescriptionController::class, 'list']);
        Route::patch('/confirm/{prescription}', [PrescriptionController::class, 'confirmPrescription']);
        Route::patch('/{prescription}', [PrescriptionController::class, 'discontinuePrescription']);
        Route::delete('/{prescription}', [PrescriptionController::class, 'destroy']);
    })->name('Prescription');

    Route::prefix('medicationchart')->group(function (){
        Route::get('', [MedicationChartController::class, 'index'])->name('MedicationChart');
        Route::post('', [MedicationChartController::class, 'store']);
        Route::get('/load/chart', [MedicationChartController::class, 'loadMedicationChartByPrescription']);
        Route::get('/load/upcoming', [MedicationChartController::class, 'loadUpcomingMedications']);
        Route::get('/{medicationChart}', [MedicationChartController::class, 'edit']);
        Route::delete('/{medicationChart}', [MedicationChartController::class, 'destroy']);
        Route::patch('/removegiven/{medicationChart}', [MedicationChartController::class, 'removeGivenData']);
        Route::patch('/{medicationChart}', [MedicationChartController::class, 'saveGivenData']);
    })->name('MedicationChart');

    Route::prefix('nursingchart')->group(function (){
        Route::get('', [NursingChartController::class, 'index'])->name('Nursingchart');
        Route::post('', [NursingChartController::class, 'store']);
        Route::get('/load/chart', [NursingChartController::class, 'loadNursingChartByPrescription']);
        Route::get('/load/upcoming', [NursingChartController::class, 'loadUpcomingNursingCharts']);
        Route::get('/{nursingChart}', [NursingChartController::class, 'edit']);
        Route::delete('/{nursingChart}', [NursingChartController::class, 'destroy']);
        Route::patch('/removeservice/{nursingChart}', [NursingChartController::class, 'removeServiceData']);
        Route::patch('/{nursingChart}', [NursingChartController::class, 'saveServiceData']);
    })->name('NursingChart');

    Route::prefix('surgerynote')->group(function () {
        Route::post('', [SurgeryNoteController::class, 'store']);
        Route::get('load/details', [SurgeryNoteController::class, 'loadSurgeryNoteTable']);
        Route::get('/{surgeryNote}', [SurgeryNoteController::class, 'edit']);
        Route::patch('/{surgeryNote}', [SurgeryNoteController::class, 'update']);
        Route::delete('/{surgeryNote}', [SurgeryNoteController::class, 'destroy']);
    });

    Route::prefix('deliverynote')->group(function () {
        Route::post('', [DeliveryNoteController::class, 'store']);
        Route::get('load/details', [DeliveryNoteController::class, 'loadDeliveryNoteTable']);
        Route::get('/{deliveryNote}', [DeliveryNoteController::class, 'edit']);
        Route::patch('/{deliveryNote}', [DeliveryNoteController::class, 'update']);
        Route::delete('/{deliveryNote}', [DeliveryNoteController::class, 'destroy']);
    });

    Route::prefix('ancregisteration')->group(function () {
        Route::post('', [AntenatalRegisterationController::class, 'store']);
        Route::get('/{antenatalRegisteration}', [AntenatalRegisterationController::class, 'edit']);
        Route::patch('/{antenatalRegisteration}', [AntenatalRegisterationController::class, 'update']);
        Route::delete('/{antenatalRegisteration}', [AntenatalRegisterationController::class, 'destroy']);
    });

    Route::prefix('ancvitalsigns')->group(function () {
        Route::post('', [AncVitalSignsController::class, 'store']);
        Route::get('/load/table', [AncVitalSignsController::class, 'loadAncVitalSignsTableByVisit']);
        Route::get('/load/chart', [AncVitalSignsController::class, 'loadAncVitalSignsChartByVisit']);
        Route::delete('/{ancVitalSigns}', [AncVitalSignsController::class, 'destroy']);
    });

    Route::prefix('bulkrequests')->group(function () {
        Route::get('/list/bulk', [BulkRequestController::class, 'listBulk']);
        Route::post('/{resource}', [BulkRequestController::class, 'store']);
        Route::get('/load/nurses', [BulkRequestController::class, 'nursesBulkRequests']);
        Route::get('/load/lab', [BulkRequestController::class, 'labBulkRequests']);
        Route::get('/load/pharmacy', [BulkRequestController::class, 'pharmacyBulkRequests']);
        Route::patch('/approve/{bulkRequest}', [BulkRequestController::class, 'toggleApproveBulkRequest']);
        Route::patch('/dispense/{bulkRequest}', [BulkRequestController::class, 'dispenseBulkRequest']);
        Route::delete('/{bulkRequest}', [BulkRequestController::class, 'destroy']);
    });

    Route::prefix('medicalreports')->group(function () {
        Route::post('', [MedicalReportController::class, 'store']);
        Route::get('load', [MedicalReportController::class, 'loadMedicalReportTable']);
        Route::patch('/{medicalReport}', [MedicalReportController::class, 'update']);
        Route::delete('/{medicalReport}', [MedicalReportController::class, 'destroy']);
        Route::get('/{medicalReport}', [MedicalReportController::class, 'edit']);
        Route::get('display/{medicalReport}', [MedicalReportController::class, 'displayReport']);
    });

    Route::prefix('nursesreport')->group(function () {
        Route::post('/{visit}', [NursesReportController::class, 'store']);
        Route::get('load', [NursesReportController::class, 'loadNursesReportTable']);
        Route::patch('/{nursesReport}', [NursesReportController::class, 'update']);
        Route::delete('/{nursesReport}', [NursesReportController::class, 'destroy']);
        Route::get('/{nursesReport}', [NursesReportController::class, 'edit']);
    });

    Route::prefix('thirdpartyservices')->group(function () {
        Route::get('', [ThirdPartyServiceController::class, 'index'])->name('Third Party Services');
        Route::post('/{prescription}', [ThirdPartyServiceController::class, 'store']);
        Route::get('/load/list', [ThirdPartyServiceController::class, 'load']);
        Route::get('/list/thirdparties', [ThirdPartyServiceController::class, 'list']);
        Route::delete('/{thirdPartyService}', [ThirdPartyServiceController::class, 'destroy']);
    });

    Route::prefix('thirdparties')->group(function () {
        Route::post('', [ThirdPartyController::class, 'store']);
        Route::get('/load/thirdparties', [ThirdPartyController::class, 'load']);
        Route::get('/list/thirdparties', [ThirdPartyController::class, 'list']);
        Route::get('/{thirdParty}', [ThirdPartyController::class, 'edit']);
        Route::post('toggle/{thirdParty}', [ThirdPartyController::class, 'toggleDelisted']);
        Route::delete('/{thirdParty}', [ThirdPartyController::class, 'destroy']);
        Route::post('/{thirdParty}', [ThirdPartyController::class, 'update']);
    });

    Route::prefix('patientsfiles')->group(function () {
        Route::post('/{visit}', [PatientsFileController::class, 'store']);
        Route::get('/load/files', [PatientsFileController::class, 'load']);
        Route::get('/download/{patientsFile}', [PatientsFileController::class, 'download']);
        Route::delete('/{patientsFile}', [PatientsFileController::class, 'destroy']);
    });

    Route::prefix('shiftreport')->group(function () {
        Route::post('', [ShiftReportController::class, 'store']);
        Route::get('load', [ShiftReportController::class, 'loadShiftReportTable']);
        Route::patch('/{shiftReport}', [ShiftReportController::class, 'update']);
        Route::delete('/{shiftReport}', [ShiftReportController::class, 'destroy']);
        Route::get('/{shiftReport}', [ShiftReportController::class, 'edit']);
        Route::get('view/{shiftReport}', [ShiftReportController::class, 'view'])->name('View Shift Report');
    });

    Route::prefix('medicationcategory')->group(function (){
        Route::post('', [MedicationCategoryController::class, 'store']);
        Route::get('/load', [MedicationCategoryController::class, 'load']);
        Route::get('/list', [MedicationCategoryController::class, 'list']);
        Route::get('/{medicationCategory}', [MedicationCategoryController::class, 'edit']);
        Route::delete('/{medicationCategory}', [MedicationCategoryController::class, 'destroy']);
        Route::post('/{medicationCategory}', [MedicationCategoryController::class, 'update']);
    })->name('Medication Category');

    Route::prefix('shiftperformance')->group(function () {
        Route::get('load', [ShiftPerformanceController::class, 'loadShiftPerformanceTable']);
        Route::get('/Nurse', [ShiftPerformanceController::class, 'UpdateDeptPreformance']);
    });
});

require __DIR__.'/auth.php';
