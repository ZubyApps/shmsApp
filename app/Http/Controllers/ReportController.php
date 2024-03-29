<?php

namespace App\Http\Controllers;

use App\Services\AccountsReportService;
use App\Services\CapitationPaymentService;
use App\Services\DatatablesService;
use App\Services\ExpenseService;
use App\Services\HospitalAndOthersReportService;
use App\Services\InvestigationReportService;
use App\Services\MedReportService;
use App\Services\PatientReportService;
use App\Services\PharmacyReportService;
use App\Services\PrescriptionService;
use App\Services\ResourceReportService;
use App\Services\UserReportService;
use App\Services\UserService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(
        private readonly SponsorCategoryController $sponsorCategoryController, 
        private readonly DatatablesService $datatablesService, 
        private readonly PatientReportService $PatientReportService,
        private readonly MedReportService $medReportService,
        private readonly InvestigationReportService $investigationReportService,
        private readonly PharmacyReportService $pharmacyReportService,
        private readonly HospitalAndOthersReportService $hospitalAndOthersReportService,
        private readonly ResourceReportService $resourceReportService,
        private readonly AccountsReportService $accountsReportService,
        private readonly CapitationPaymentService $capitationPaymentService,
        private readonly ExpenseCategoryController $expenseCategoryController,
        private readonly UserService $userService,
        private readonly PrescriptionService $prescriptionService,
        private readonly ExpenseService $expenseService,
        private readonly UserReportService $userReportService,
        )
    {
        
    }

    public function index()
    {
        return view('reports.reports');
    }

    /** Patients reports */    
    public function indexPatients()
    {
        return view('reports.patients');
    }

    public function loadPatientsDistribution1(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->PatientReportService->getPatientsDistribution1($params, $request);

        return response()->json([
            'data' => $sponsors,
            'draw' => $params->draw,
            'recordsTotal' => count($sponsors),
            'recordsFiltered' => count($sponsors)
        ]);
    }

    public function loadPatientsDistribution2(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $sponsors = $this->PatientReportService->getPatientsDistribution2($params, $request);

        return response()->json([
            'data' => $sponsors,
            'draw' => $params->draw,
            'recordsTotal' => $sponsors->total(),
            'recordsFiltered' => $sponsors->total()
        ]);
    }

    public function loadPatientFrequency(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $patients = $this->PatientReportService->getPatientFrequency($params, $request);
        
        $loadTransformer = $this->PatientReportService->getFrequencyTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadBySponsor(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->PatientReportService->getBySponsor($params, $request);

        $loadTransformer = $this->PatientReportService->getBySponsorTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadBySponsorMonth(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->PatientReportService->getBySponsorMonth($params, $request);

        $loadTransformer = $this->PatientReportService->getBySponsorTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadRegSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $summary = $this->PatientReportService->getRegSummary($params, $request);

        $loadTransformer = $this->PatientReportService->getRegTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $summary, $params);
    }

    /** Medical Services Report */
    public function indexMedServices()
    {
        return view('reports.medServices');
    }

    public function loadMedServicesSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->medReportService->getMedServicesSummary($params, $request);

        $loadTransformer = $this->medReportService->getMedServicesTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadByResource(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->medReportService->getPatientsByResource($params, $request);

        $loadTransformer = $this->medReportService->getByResourceTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadNewBirths(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->medReportService->getNewBirthsList($params, $request);

        $loadTransformer = $this->medReportService->getNewBirthsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadVisitsByDischarge(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->medReportService->getVisitsByDischarge($params, $request);

        $loadTransformer = $this->medReportService->getByDischargeTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadDischargeSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $visits = $this->medReportService->getDischargeSummary($params, $request);

        return response()->json([
            'data' => $visits,
            'draw' => $params->draw,
            'recordsTotal' => $visits->total(),
            'recordsFiltered' => $visits->total()
        ]);
    }

    /**Investigations Report */
    public function indexInvestigations()
    {
        return view('reports.investigations');
    }

    public function loadInvestigationsSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->investigationReportService->getInvestigationsSummary($params, $request);

        $loadTransformer = $this->investigationReportService->getInvestigationsTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    /** Pharmacy Report */
    public function indexPharmacy()
    {
        return view('reports.pharmacy');
    }

    public function loadPharmacySummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->pharmacyReportService->getPharmacySummary($params, $request);

        $loadTransformer = $this->pharmacyReportService->getPharmacyTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    /** Hospital Services and Others Report */
    public function indexHospitalAndOthers()
    {
        return view('reports.hospitalAndOthers');
    }

    public function loadHospitalAndOthersSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->hospitalAndOthersReportService->getHospitalAndOthersSummary($params, $request);

        $loadTransformer = $this->hospitalAndOthersReportService->getHospitalAndOthersTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    //Resource Reports
    public function indexResources()
    {
        return view('reports.resources');
    }
    
    public function loadResourceValueSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $resources = $this->resourceReportService->getResourceValueSummary($params, $request);
        
        return response()->json([
            'data' => $resources,
            'draw' => $params->draw,
            'recordsTotal' => count($resources),
            'recordsFiltered' => count($resources)
        ]);
    }
    
    public function loadUsedResourcesSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
        
        $categories = $this->resourceReportService->getUsedResourcesSummary($params, $request);
        
        return response()->json([
            'data' => $categories,
            'draw' => $params->draw,
            'recordsTotal' => count($categories),
            'recordsFiltered' => count($categories)
        ]);
        
    }

    public function loadByCategoryResource(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->resourceReportService->getPrescriptionsByResourceCategory($params, $request);

        $loadTransformer = $this->resourceReportService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadByExpirationOrStock(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $expirationStock = $this->resourceReportService->getResourcesByExpirationOrStock($params, $request);
       
        $transformer = $this->resourceReportService->getExpirationStockTransformer();

        return $this->datatablesService->datatableResponse($transformer, $expirationStock, $params);  
    }

    // Account Reports
    public function indexAccounts()
    {
        return view('reports.accounts', [
            'users' => $this->userService->listStaff(special_note:'Management'),
            'categories' => $this->expenseCategoryController->showAll('id', 'name')
        ]);
    }

    public function loadPayMethodsSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);

        $payMethods = $this->accountsReportService->getPaymethodsSummary($params, $request);
        
        return response()->json([
            'data' => $payMethods,
            'draw' => $params->draw,
            'recordsTotal' => count($payMethods),
            'recordsFiltered' => count($payMethods)
        ]);
    }

    public function loadCapitationPayments(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->capitationPaymentService->getCapitationPayments($params, $request);

        $loadTransformer = $this->capitationPaymentService->getLoadTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadTPSSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
        
        $TPSSummary = $this->accountsReportService->getTPSSummary($params, $request);
        
        return response()->json([
            'data' => $TPSSummary,
            'draw' => $params->draw,
            'recordsTotal' => $TPSSummary->total(),
            'recordsFiltered' => $TPSSummary->total()
        ]);        
    }

    public function loadExpensesSummary(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
        
        $expenseSummaries = $this->accountsReportService->getExpenseSummary($params, $request);
        
        return response()->json([
            'data' => $expenseSummaries,
            'draw' => $params->draw,
            'recordsTotal' => $expenseSummaries->total(),
            'recordsFiltered' => $expenseSummaries->total()
        ]);        
    }

    public function loadVisitsSummaryBySponsorCategory(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
        
        $visitsSummaries = $this->accountsReportService->getVisitsSummaryBySponsorCategory($params, $request);
        
        return response()->json([
            'data' => $visitsSummaries,
            'draw' => $params->draw,
            'recordsTotal' => $visitsSummaries->total(),
            'recordsFiltered' => $visitsSummaries->total()
        ]);        
    }

    public function loadVisitsSummaryBySponsor(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
        
        $visitsSummaries = $this->accountsReportService->getVisitsSummaryBySponsor($params, $request);
        
        return response()->json([
            'data' => $visitsSummaries,
            'draw' => $params->draw,
            'recordsTotal' => $visitsSummaries->total(),
            'recordsFiltered' => $visitsSummaries->total()
        ]);        
    }

    public function loadPaymentsByPayMethod(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->accountsReportService->getPaymentsByPayMethod($params, $request);

        $loadTransformer = $this->accountsReportService->getPaymentsByPayMethodTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadTPSByThirdParty(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->accountsReportService->getThirdPartyServicesByThirdParty($params, $request);

        $loadTransformer = $this->accountsReportService->getTPSByThirdPartyTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadVisitsBySponsor(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->accountsReportService->getVisitsBySponsor($params, $request);

        $loadTransformer = $this->accountsReportService->getVisitsBySponsorTransformer();

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadYearlyIncomeAndExpense(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
        
        $totalIncomes   = $this->prescriptionService->totalYearlyIncomeFromPrescription($request);
        $totalExpenses  = $this->expenseService->totalYearlyExpense($request);

        $incomeArray = [...$totalIncomes, ...$totalExpenses];

        $months = [
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'January', 'm' => 1],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'February', 'm' => 2],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'March', 'm' => 3],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'April', 'm' => 4],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'May', 'm' => 5],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'June', 'm' => 6],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'July', 'm' => 7],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'August', 'm' => 8],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'September', 'm' => 9],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'October', 'm' => 10],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'November', 'm' => 11],
            ['bill' => 0, 'paid' => 0, 'expense' => 0, 'month_name' => 'December', 'm' => 12],
        ];


        foreach($incomeArray as $income){
            foreach($months as $key => $month){
                if ($month['m'] === $income->month){
                    $months[$key]['bill'] === 0 && $income->bill ? $months[$key]['bill'] = $income->bill : 0 ;
                    
                    $months[$key]['paid'] === 0 && $income->paid ? $months[$key]['paid'] = $income->paid : 0 ;

                    $months[$key]['expense'] = $income->amount ?? 0;
                }
            }
        }

        $total = count($months);

        return response()->json([
            'data' => $months,
            'draw' => $params->draw,
            'recordsTotal' => $total,
            'recordsFiltered' => $total
        ]);
    }

    // Staff/Users Reports
    public function indexUsers()
    {
        return view('reports.users');
    }

    public function loadDoctorsActivity(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->userReportService->staffActivitiesByDesignation($params, $request);

        $loadTransformer = $this->userReportService->getDoctorsTransformer($request);

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadNursesActivity(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->userReportService->staffActivitiesByDesignation($params, $request);

        $loadTransformer = $this->userReportService->getNursesTransformer($request);

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadLabTechsActivity(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->userReportService->staffActivitiesByDesignation($params, $request);

        $loadTransformer = $this->userReportService->getLabTechsTransformer($request);

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadPharmacyTechsActivity(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->userReportService->staffActivitiesByDesignation($params, $request);

        $loadTransformer = $this->userReportService->getPharmacyTechsTransformer($request);

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadHmoOfficersActivity(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->userReportService->staffActivitiesByDesignation($params, $request);

        $loadTransformer = $this->userReportService->getHmoOfficerTransformer($request);

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }

    public function loadBillOfficersActivity(Request $request)
    {
        $params = $this->datatablesService->getDataTableQueryParameters($request);
    
        $patients = $this->userReportService->staffActivitiesByDesignation($params, $request);

        $loadTransformer = $this->userReportService->getBillOfficerTransformer($request);

        return $this->datatablesService->datatableResponse($loadTransformer, $patients, $params);
    }
}
