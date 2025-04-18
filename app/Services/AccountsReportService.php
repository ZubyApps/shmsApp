<?php

declare(strict_types = 1);

namespace App\Services;

use App\DataObjects\DataTableQueryParams;
use App\Models\Payment;
use App\Models\PayMethod;
use App\Models\Prescription;
use App\Models\ThirdPartyService;
use App\Models\Visit;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;

class AccountsReportService
{
    public function __construct(
        private readonly PayMethod $payMethod, 
        private readonly Payment $payment, 
        private readonly HelperService $helperService,
        private readonly Prescription $prescription,
        private readonly ExpenseService $expenseService,
        private readonly PrescriptionService $prescriptionService,
        private readonly ThirdPartyService $thirdPartyService,
        private readonly Visit $visit,
        private readonly PayPercentageService $payPercentageService
        )
    {
    }

    public function getPayMethodsSummary(DataTableQueryParams $params, $data)
    {
        $current = new CarbonImmutable();

        if ($data->startDate && $data->endDate){
        return DB::table('pay_methods')
            ->selectRaw('COUNT(payments.id) as paymentCount, pay_methods.name AS pMethod, pay_methods.id AS id, SUM(payments.amount_paid) as amountPaid')
            ->leftJoin('payments', 'pay_methods.id', '=', 'payments.pay_method_id')
            ->whereBetween('payments.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
            ->groupBy('pMethod', 'id')
            ->orderBy('paymentCount', 'desc')
            ->get()
            ->toArray();
        }

        if($data->date){
            $date = new Carbon($data->date);

            return DB::table('pay_methods')
            ->selectRaw('COUNT(payments.id) as paymentCount, pay_methods.name AS pMethod, pay_methods.id AS id, SUM(payments.amount_paid) as amountPaid')
            ->leftJoin('payments', 'pay_methods.id', '=', 'payments.pay_method_id')
            ->whereMonth('payments.created_at', $date->month)
            ->whereYear('payments.created_at', $date->year)
            ->groupBy('pMethod', 'id')
            ->orderBy('paymentCount', 'desc')
            ->get()
            ->toArray();
        }

        return DB::table('pay_methods')
            ->selectRaw('COUNT(DISTINCT(payments.id)) as paymentCount, pay_methods.name AS pMethod, pay_methods.id AS id, SUM(payments.amount_paid) as amountPaid')
            ->leftJoin('payments', 'pay_methods.id', '=', 'payments.pay_method_id')
            ->whereMonth('payments.created_at', $current->month)
            ->whereYear('payments.created_at', $current->year)
            ->groupBy('pMethod', 'id')
            ->orderBy('paymentCount', 'desc')
            ->get()
            ->toArray();
    }

    public function getTPSSummary(DataTableQueryParams $params, $data)
    {
        $current = new CarbonImmutable();

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                return DB::table('third_party_services')
                ->selectRaw('third_parties.short_name AS thirdParty, third_parties.full_name AS thirdPartyL, third_parties.id as id, COUNT(prescriptions.id) as servicesCount, SUM(prescriptions.hms_bill) as totalHmsBill, COUNT(DISTINCT(patients.id)) as patientsCount')
                ->leftJoin('third_parties', 'third_party_services.third_party_id', '=', 'third_parties.id')
                ->leftJoin('prescriptions', 'third_party_services.prescription_id', '=', 'prescriptions.id')
                ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                ->leftJoin('patients', 'visits.patient_id', '=', 'patients.id')
                ->whereBetween('third_party_services.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->where('third_parties.short_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                ->groupBy('thirdParty', 'id', 'thirdPartyL')
                ->orderBy('thirdParty', 'desc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
    
                return DB::table('third_party_services')
                    ->selectRaw('third_parties.short_name AS thirdParty, third_parties.full_name AS thirdPartyL, third_parties.id as id, COUNT(prescriptions.id) as servicesCount, SUM(prescriptions.hms_bill) as totalHmsBill, COUNT(DISTINCT(patients.id)) as patientsCount')
                    ->leftJoin('third_parties', 'third_party_services.third_party_id', '=', 'third_parties.id')
                    ->leftJoin('prescriptions', 'third_party_services.prescription_id', '=', 'prescriptions.id')
                    ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                    ->leftJoin('patients', 'visits.patient_id', '=', 'patients.id')
                    ->whereMonth('third_party_services.created_at', $date->month)
                    ->whereYear('third_party_services.created_at', $date->year)
                    ->where('third_parties.short_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                    ->groupBy('thirdParty', 'id', 'thirdPartyL')
                    ->orderBy('thirdParty', 'desc')
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            
            return DB::table('third_party_services')
            ->selectRaw('third_parties.short_name AS thirdParty, third_parties.full_name AS thirdPartyL, third_parties.id as id, COUNT(prescriptions.id) as servicesCount, SUM(prescriptions.hms_bill) as totalHmsBill, COUNT(DISTINCT(patients.id)) as patientsCount')
            ->leftJoin('third_parties', 'third_party_services.third_party_id', '=', 'third_parties.id')
            ->leftJoin('prescriptions', 'third_party_services.prescription_id', '=', 'prescriptions.id')
            ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
            ->leftJoin('patients', 'visits.patient_id', '=', 'patients.id')
            ->whereMonth('third_party_services.created_at', $current->month)
            ->whereYear('third_party_services.created_at', $current->year)
            ->where('third_parties.short_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
            ->groupBy('thirdParty', 'id', 'thirdPartyL')
            ->orderBy('thirdParty', 'desc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    
        }

        if ($data->startDate && $data->endDate){
            return DB::table('third_party_services')
                ->selectRaw('third_parties.short_name AS thirdParty, third_parties.full_name AS thirdPartyL, third_parties.id as id, COUNT(prescriptions.id) as servicesCount, SUM(prescriptions.hms_bill) as totalHmsBill, COUNT(DISTINCT(patients.id)) as patientsCount')
                ->leftJoin('third_parties', 'third_party_services.third_party_id', '=', 'third_parties.id')
                ->leftJoin('prescriptions', 'third_party_services.prescription_id', '=', 'prescriptions.id')
                ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                ->leftJoin('patients', 'visits.patient_id', '=', 'patients.id')
                ->whereBetween('third_party_services.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->groupBy('thirdParty', 'id', 'thirdPartyL')
                ->orderBy('thirdParty', 'desc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return DB::table('third_party_services')
                ->selectRaw('third_parties.short_name AS thirdParty, third_parties.full_name AS thirdPartyL, third_parties.id as id, COUNT(prescriptions.id) as servicesCount, SUM(prescriptions.hms_bill) as totalHmsBill, COUNT(DISTINCT(patients.id)) as patientsCount')
                ->leftJoin('third_parties', 'third_party_services.third_party_id', '=', 'third_parties.id')
                ->leftJoin('prescriptions', 'third_party_services.prescription_id', '=', 'prescriptions.id')
                ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
                ->leftJoin('patients', 'visits.patient_id', '=', 'patients.id')
                ->whereMonth('third_party_services.created_at', $date->month)
                ->whereYear('third_party_services.created_at', $date->year)
                ->groupBy('thirdParty', 'id', 'thirdPartyL')
                ->orderBy('thirdParty', 'desc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }


        return DB::table('third_party_services')
            ->selectRaw('third_parties.short_name AS thirdParty, third_parties.full_name AS thirdPartyL, third_parties.id as id, COUNT(prescriptions.id) as servicesCount, SUM(prescriptions.hms_bill) as totalHmsBill, COUNT(DISTINCT(patients.id)) as patientsCount')
            ->leftJoin('third_parties', 'third_party_services.third_party_id', '=', 'third_parties.id')
            ->leftJoin('prescriptions', 'third_party_services.prescription_id', '=', 'prescriptions.id')
            ->leftJoin('visits', 'prescriptions.visit_id', '=', 'visits.id')
            ->leftJoin('patients', 'visits.patient_id', '=', 'patients.id')
            ->whereMonth('third_party_services.created_at', $current->month)
            ->whereYear('third_party_services.created_at', $current->year)
            ->groupBy('thirdParty', 'id', 'thirdPartyL')
            ->orderBy('thirdParty', 'desc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getExpenseSummary(DataTableQueryParams $params, $data)
    {
        $current = new CarbonImmutable();

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                return DB::table('expenses')
                ->selectRaw('expense_categories.name AS eCategory, expense_categories.id AS id, COUNT(expenses.id) as expenseCount, SUM(expenses.amount) as totalExpense')
                ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                ->whereBetween('expenses.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->where('expense_categories.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                ->groupBy('eCategory', 'id')
                ->orderBy('eCategory', 'desc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
    
                return DB::table('expenses')
                ->selectRaw('expense_categories.name AS eCategory, expense_categories.id AS id, COUNT(expenses.id) as expenseCount, SUM(expenses.amount) as totalExpense')
                    ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                    ->whereMonth('expenses.created_at', $date->month)
                    ->whereYear('expenses.created_at', $date->year)
                    ->where('expense_categories.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                    ->groupBy('eCategory', 'id')
                    ->orderBy('eCategory', 'desc')
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            
            return DB::table('expenses')
            ->selectRaw('expense_categories.name AS eCategory, expense_categories.id AS id, COUNT(expenses.id) as expenseCount, SUM(expenses.amount) as totalExpense')
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->whereMonth('expenses.created_at', $current->month)
            ->whereYear('expenses.created_at', $current->year)
            ->where('expense_categories.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
            ->groupBy('eCategory', 'id')
            ->orderBy('eCategory', 'desc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return DB::table('expenses')
            ->selectRaw('expense_categories.name AS eCategory, expense_categories.id AS id, COUNT(expenses.id) as expenseCount, SUM(expenses.amount) as totalExpense')
                ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                ->whereBetween('expenses.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->groupBy('eCategory', 'id')
                ->orderBy('eCategory', 'desc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return DB::table('expenses')
            ->selectRaw('expense_categories.name AS eCategory, expense_categories.id AS id, COUNT(expenses.id) as expenseCount, SUM(expenses.amount) as totalExpense')
                ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
                ->whereMonth('expenses.created_at', $date->month)
                ->whereYear('expenses.created_at', $date->year)
                ->groupBy('eCategory', 'id')
                ->orderBy('eCategory', 'desc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }


        return DB::table('expenses')
        ->selectRaw('expense_categories.name AS eCategory, expense_categories.id AS id, COUNT(expenses.id) as expenseCount, SUM(expenses.amount) as totalExpense')
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->whereMonth('expenses.created_at', $current->month)
            ->whereYear('expenses.created_at', $current->year)
            ->groupBy('eCategory', 'id')
            ->orderBy('eCategory', 'desc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPaymentsByPayMethod(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current    = CarbonImmutable::now();

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                return $this->payment
                            ->whereRelation('payMethod', 'id', '=', $data->payMethodId)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
    
                return $this->payment
                    ->whereRelation('payMethod', 'id', '=', $data->payMethodId)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->payment
                            ->whereRelation('payMethod', 'id', '=', $data->payMethodId)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('visit.patient.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereMonth('created_at', $current->month)
                            ->whereYear('created_at', $current->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $this->payment
                ->whereRelation('payMethod', 'id', '=', $data->payMethodId)
                ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return $this->payment
                ->whereRelation('payMethod', 'id', '=', $data->payMethodId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $this->payment
                ->whereRelation('payMethod', 'id', '=', $data->payMethodId)
                ->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getPaymentsByPayMethodTransformer(): callable
    {
        return  function (Payment $payment) {

            $pVisit = $payment?->visit;

            return [
                    'id'                => $payment->id,
                    'date'              => (new Carbon($payment->created_at))->format('d/M/y g:ia'),
                    'patient'           => $pVisit?->patient->patientId(),
                    'sponsor'           => $pVisit?->sponsor->name,
                    'category'          => $pVisit?->sponsor->category_name,
                    'diagnosis'         => $pVisit?->consultations()->where('visit_id', $pVisit->id)->first()?->icd11_diagnosis ?? $pVisit?->consultations()->where('visit_id', $pVisit?->id)->first()?->provisional_diagnosis,
                    'doctor'            => $pVisit?->doctor?->username,
                    'totalHmsBill'      => $pVisit?->total_hms_bill,
                    'totalHmoBill'      => $pVisit?->total_hmo_bill,
                    'totalNhisBill'     => $pVisit?->total_nhis_bill,
                    'amountPaid'        => $payment->amount_paid,
                    'comment'           => $payment->comment,
                    'by'                => $payment->user->username,
                ];
            };
    }

    public function getThirdPartyServicesByThirdParty(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'desc';
        $current    = CarbonImmutable::now();

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                return $this->thirdPartyService
                            ->whereRelation('thirdParty', 'id', '=',  $data->thirdPartyId)
                            ->orWhere(function (Builder $query) use($params, $data) {
                                $query->whereRelation('prescription.visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.consultation', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.consultation', 'provisional_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
                return $this->thirdPartyService
                            ->whereRelation('thirdParty', 'id', '=',  $data->thirdPartyId)
                            ->orWhere(function (Builder $query) use($params, $data) {
                                $query->whereRelation('prescription.visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.consultation', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.consultation', 'provisional_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereMonth('created_at', $date->month)
                            ->whereYear('created_at', $date->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->thirdPartyService
                            ->whereRelation('thirdParty', 'id', '=',  $data->thirdPartyId)
                            ->orWhere(function (Builder $query) use($params, $data) {
                                $query->whereRelation('prescription.visit.patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.sponsor', 'name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.visit.sponsor', 'category_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.consultation', 'icd11_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('prescription.consultation', 'provisional_diagnosis', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereMonth('created_at', $current->month)
                            ->whereYear('created_at', $current->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return $this->thirdPartyService
                        ->whereRelation('thirdParty', 'id', '=',  $data->thirdPartyId)
                        ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                        ->orderBy($orderBy, $orderDir)
                        ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);
            return $this->thirdPartyService
                    ->whereRelation('thirdParty', 'id', '=',  $data->thirdPartyId)
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return $this->thirdPartyService
                    ->whereRelation('thirdParty', 'id', '=',  $data->thirdPartyId)
                    ->whereMonth('created_at', $current->month)
                    ->whereYear('created_at', $current->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));

       
    }

    public function getTPSByThirdPartyTransformer(): callable
    {
       return  function (ThirdPartyService $thirdPartyService) {
            return [
                'id'                => $thirdPartyService->id,
                'date'              => (new Carbon($thirdPartyService->created_at))->format('d/m/Y'),
                'thirdParty'        => $thirdPartyService->thirdParty->short_name,
                'sponsorCategoryClass'  => $thirdPartyService->prescription->visit->sponsor->sponsorCategory->pay_class,
                'sponsorCategory'       => $thirdPartyService->prescription->visit->sponsor->category_name,
                'sponsor'               => $thirdPartyService->prescription->visit->sponsor->name,
                'resource'              => $thirdPartyService->prescription->resource->name,
                'patient'               => $thirdPartyService->prescription->visit->patient->patientId(),
                'doctor'                => $thirdPartyService->prescription->user->username,
                'diagnosis'             => $thirdPartyService->prescription->consultation?->icd11_diagnosis ?? $thirdPartyService->prescription->consultation?->provisional_diagnosis ?? $thirdPartyService->prescription->consultation?->assessment,
                'admissionStatus'   => $thirdPartyService->prescription->visit->admission_status,
                'reason'            => $thirdPartyService->prescription->visit->discharge_reason,
                'hmsBill'           => $thirdPartyService->prescription->hms_bill,
                'initiatedBy'       => $thirdPartyService->user->username,
                'payPercent'        => $this->payPercentageService->individual_Family($thirdPartyService->prescription->visit),
                'payPercentNhis'    => $this->payPercentageService->nhis($thirdPartyService->prescription->visit),
                'payPercentHmo'     => $this->payPercentageService->hmo_Retainership($thirdPartyService->prescription->visit),
                'paid'              => $thirdPartyService->prescription->paid > 0 && $thirdPartyService->prescription->paid >= $thirdPartyService->prescription->hms_bill,
                'paidNhis'          => $thirdPartyService->prescription->paid > 0 && $thirdPartyService->prescription->paid >= $thirdPartyService->prescription->hms_bill/10 && $thirdPartyService->prescription->visit->sponsor->sponsorCategory->name == 'NHIS',
                'approved'          => $thirdPartyService->prescription->approved, 
                'rejected'          => $thirdPartyService->prescription->rejected,
                'user'              => auth()->user()->designation->access_level > 4
            ];
         };
    }

    public function getVisitsSummaryBySponsorCategory(DataTableQueryParams $params, $data)
    {
        $current    = CarbonImmutable::now();

        if ($data->startDate && $data->endDate){
            return DB::table('visits')
            ->selectRaw('COUNT(DISTINCT(visits.sponsor_id)) as sponsorCount, sponsor_categories.name as category, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, SUM(CASE WHEN visits.consulted IS NOT NULL THEN 1 ELSE 0 END) AS visitConsulted, SUM(visits.total_hms_bill) AS totalHmsBill, SUM(visits.total_hmo_bill) AS totalHmoBill, SUM(visits.total_nhis_bill) AS totalNhisBill, SUM(visits.total_paid) AS totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) AS discount')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->whereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
            ->groupBy('category')
            ->orderBy('patientsCount', 'desc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return DB::table('visits')
            ->selectRaw('COUNT(DISTINCT(visits.sponsor_id)) as sponsorCount, sponsor_categories.name as category, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, SUM(CASE WHEN visits.consulted IS NOT NULL THEN 1 ELSE 0 END) AS visitConsulted, SUM(visits.total_hms_bill) AS totalHmsBill, SUM(visits.total_hmo_bill) AS totalHmoBill, SUM(visits.total_nhis_bill) AS totalNhisBill, SUM(visits.total_paid) AS totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) AS discount')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->whereMonth('visits.created_at', $date->month)
            ->whereYear('visits.created_at', $date->year)
            ->groupBy('category')
            ->orderBy('patientsCount', 'desc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return DB::table('visits')
            ->selectRaw('COUNT(DISTINCT(visits.sponsor_id)) as sponsorCount, sponsor_categories.name as category, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, SUM(CASE WHEN visits.consulted IS NOT NULL THEN 1 ELSE 0 END) AS visitConsulted, SUM(visits.total_hms_bill) AS totalHmsBill, SUM(visits.total_hmo_bill) AS totalHmoBill, SUM(visits.total_nhis_bill) AS totalNhisBill, SUM(visits.total_paid) AS totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) AS discount')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->whereMonth('visits.created_at', $current->month)
            ->whereYear('visits.created_at', $current->year)
            ->groupBy('category')
            ->orderBy('patientsCount', 'desc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisitsSummaryBySponsor(DataTableQueryParams $params, $data)
    {
        $current = CarbonImmutable::now();

        if (! empty($params->searchTerm)) {

            if ($data->startDate && $data->endDate){
                return DB::table('visits')
                ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, SUM(visits.total_hms_bill) AS totalHmsBill, SUM(visits.total_hmo_bill) AS totalHmoBill, SUM(visits.total_nhis_bill) AS totalNhisBill, SUM(visits.total_paid) AS totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, SUM(CASE WHEN visits.resolved IS FALSE THEN 1 ELSE 0 END) AS resolved, sponsors.flag as flagSponsor')
                ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
                ->where(function (QueryBuilder $query) use($params) {
                    $query->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                    ->orWhere('sponsor_categories.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                })
                ->whereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->groupBy('sponsor', 'id', 'category', 'flagSponsor')
                ->orderBy('sponsor', 'asc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
    
            if($data->date){
                $date = new Carbon($data->date);
    
                return DB::table('visits')
                ->selectRaw('sponsors.name as sponsor, sponsors.id as id, sponsor_categories.name as category, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, SUM(visits.total_hms_bill) AS totalHmsBill, SUM(visits.total_hmo_bill) AS totalHmoBill, SUM(visits.total_nhis_bill) AS totalNhisBill, SUM(visits.total_paid) AS totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, SUM(CASE WHEN visits.resolved IS FALSE THEN 1 ELSE 0 END) AS resolved, sponsors.flag as flagSponsor')
                ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
                ->where(function (QueryBuilder $query) use($params) {
                    $query->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                    ->orWhere('sponsor_categories.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                })
                ->whereMonth('visits.created_at', $date->month)
                ->whereYear('visits.created_at', $date->year)
                ->groupBy('sponsor', 'id', 'category', 'flagSponsor')
                ->orderBy('sponsor', 'asc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
    
            return DB::table('visits')
                ->selectRaw('sponsors.id as id, sponsors.name as sponsor, sponsor_categories.name as category, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, SUM(visits.total_hms_bill) AS totalHmsBill, SUM(visits.total_hmo_bill) AS totalHmoBill, SUM(visits.total_nhis_bill) AS totalNhisBill, SUM(visits.total_paid) AS totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, SUM(CASE WHEN visits.resolved IS FALSE THEN 1 ELSE 0 END) AS resolved, sponsors.flag as flagSponsor')
                ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
                ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
                ->where(function (QueryBuilder $query) use($params) {
                    $query->where('sponsors.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                    ->orWhere('sponsor_categories.name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                })
                ->whereMonth('visits.created_at', $current->month)
                ->whereYear('visits.created_at', $current->year)
                ->groupBy('sponsor', 'id', 'category', 'flagSponsor')
                ->orderBy('sponsor', 'asc')
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){
            return DB::table('visits')
            ->selectRaw('sponsors.id as id, sponsors.name as sponsor, sponsor_categories.name as category, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, SUM(visits.total_hms_bill) AS totalHmsBill, SUM(visits.total_hmo_bill) AS totalHmoBill, SUM(visits.total_nhis_bill) AS totalNhisBill, SUM(visits.total_paid) AS totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, SUM(CASE WHEN visits.resolved IS FALSE THEN 1 ELSE 0 END) AS resolved, sponsors.flag as flagSponsor')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->whereBetween('visits.created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
            ->groupBy('sponsor', 'id', 'category', 'flagSponsor')
            ->orderBy('sponsor', 'asc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            return DB::table('visits')
            ->selectRaw('sponsors.id as id, sponsors.name as sponsor, sponsor_categories.name as category, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, SUM(visits.total_hms_bill) AS totalHmsBill, SUM(visits.total_hmo_bill) AS totalHmoBill, SUM(visits.total_nhis_bill) AS totalNhisBill, SUM(visits.total_paid) AS totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, SUM(CASE WHEN visits.resolved IS FALSE THEN 1 ELSE 0 END) AS resolved, sponsors.flag as flagSponsor')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->whereMonth('visits.created_at', $date->month)
            ->whereYear('visits.created_at', $date->year)
            ->groupBy('sponsor', 'id', 'category', 'flagSponsor')
            ->orderBy('sponsor', 'asc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        return DB::table('visits')
            ->selectRaw('sponsors.id as id, sponsors.name as sponsor, sponsor_categories.name as category, COUNT(DISTINCT(visits.patient_id)) as patientsCount, COUNT(DISTINCT(visits.id)) as visitCount, SUM(visits.total_hms_bill) AS totalHmsBill, SUM(visits.total_hmo_bill) AS totalHmoBill, SUM(visits.total_nhis_bill) AS totalNhisBill, SUM(visits.total_paid) AS totalPaid, SUM(visits.total_capitation) AS totalCapitation, SUM(visits.discount) as discount, SUM(CASE WHEN visits.resolved IS FALSE THEN 1 ELSE 0 END) AS resolved, sponsors.flag as flagSponsor')
            ->leftJoin('sponsors', 'visits.sponsor_id', '=', 'sponsors.id')
            ->leftJoin('sponsor_categories', 'sponsors.sponsor_category_id', '=', 'sponsor_categories.id')
            ->whereMonth('visits.created_at', $current->month)
            ->whereYear('visits.created_at', $current->year)
            ->groupBy('sponsor', 'id', 'category', 'flagSponsor')
            ->orderBy('sponsor', 'asc')
            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisitsBySponsor(DataTableQueryParams $params, $data)
    {
        $orderBy    = 'created_at';
        $orderDir   =  'asc';
        $current    = CarbonImmutable::now();

        if (! empty($params->searchTerm)) {
            if ($data->startDate && $data->endDate){
                return $this->visit
                            ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            if($data->date){
                $date = new Carbon($data->date);
    
                return $this->visit
                    ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                    ->where(function (Builder $query) use($params) {
                        $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                        ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                    })
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->orderBy($orderBy, $orderDir)
                    ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }

            return $this->visit
                            ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                            ->where(function (Builder $query) use($params) {
                                $query->whereRelation('patient', 'first_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'middle_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'last_name', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' )
                                ->orWhereRelation('patient', 'card_no', 'LIKE', '%' . addcslashes($params->searchTerm, '%_') . '%' );
                            })
                            ->whereMonth('created_at', $current->month)
                            ->whereYear('created_at', $current->year)
                            ->orderBy($orderBy, $orderDir)
                            ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if ($data->startDate && $data->endDate){

            if ($data->state == 'reviewed'){
                return $this->visit
                ->whereNull('reviewed')
                ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            if ($data->state == 'resolved'){
                return $this->visit
                ->where('resolved', false)
                ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->visit
                ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                ->whereBetween('created_at', [$data->startDate.' 00:00:00', $data->endDate.' 23:59:59'])
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        if($data->date){
            $date = new Carbon($data->date);

            if ($data->state == 'reviewed'){
                return $this->visit
                ->whereNull('reviewed')
                ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            if ($data->state == 'resolved'){
                return $this->visit
                ->where('resolved', false)
                ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
            }
            return $this->visit
                ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
        }

        
        return $this->visit
                ->whereRelation('sponsor', 'id', '=', $data->sponsorId)
                ->whereMonth('created_at', $current->month)
                ->whereYear('created_at', $current->year)
                ->orderBy($orderBy, $orderDir)
                ->paginate($params->length, '*', '', (($params->length + $params->start)/$params->length));
    }

    public function getVisitsBySponsorTransformer(): callable
    {
        return  function (Visit $visit) {

            return [
                    'id'                => $visit->id,
                    'category'          => $visit->sponsor->category_name,
                    'date'              => (new Carbon($visit->created_at))->format('d/M/y g:ia'),
                    'patient'           => $visit->patient->patientId(),
                    'diagnosis'         => $visit->consultations()->where('visit_id', $visit->id)->first()?->icd11_diagnosis ?? $visit->consultations()->where('visit_id', $visit->id)->first()?->provisional_diagnosis,
                    'doctor'            => $visit->doctor?->username,
                    'totalHmsBill'      => $visit->total_hms_bill,
                    'totalHmoBill'      => $visit->total_hmo_bill,
                    'totalNhisBill'     => $visit->total_nhis_bill,
                    'amountPaid'        => $visit->total_paid,
                    'discount'          => $visit->discount ?? 0,
                    'reviewed'          => $visit->reviewed,
                    'resolved'          => $visit->resolved,
                    'flagPatient'       => $visit->patient->flag,
                    'flagReason'        => $visit->patient?->flag_reason,
                ];
            };
    }
}
