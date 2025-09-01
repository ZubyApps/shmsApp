<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

Class PaymentService
{
    public function __construct(private readonly Payment $payment)
    {       
    }

    public function create(Request $data, User $user): Payment
    {
        return DB::transaction(function () use($data, $user) {
            if (!$data->patientId || !$data->visitId ){

                if ($data->backdate){
                    $payment = $user->payments()->create([
                        'amount_paid'   => $data->amount,
                        'pay_method_id' => $data->payMethod,
                        'comment'       => $data->comment,
                        'created_at'    => $data->backdate,
                    ]);
    
                    return $payment;
                }

                $payment = $user->payments()->create([
                    'amount_paid'   => $data->amount,
                    'pay_method_id' => $data->payMethod,
                    'comment'       => $data->comment,
                ]);

                return $payment;
            }

            $payment = $user->payments()->create([
                'amount_paid'   => $data->amount,
                'pay_method_id' => $data->payMethod,
                'comment'       => $data->comment,
                'patient_id'    => $data->patientId,
                'visit_id'      => $data->visitId,
            ]);

            $visit = $payment->visit;

            $totalPayments = $visit->totalPayments();

            $visit->update([
                'total_paid'        => $visit->sponsor->category_name == 'HMO' ? $visit->totalPaidPrescriptions() : $totalPayments,
                'total_hms_bill'    => $visit->totalHmsBills(),
                'total_nhis_bill'   => $visit->sponsor->category_name == 'NHIS' ? $visit->totalNhisBills() : 0,
            ]);

            $prescriptions = $visit->prescriptions;

            if ($visit->sponsor->sponsorCategory->name == 'NHIS'){
                $this->prescriptionsPaymentSeiveNhis($totalPayments, $prescriptions);
            } else {
                $this->prescriptionsPaymentSeive($totalPayments, $prescriptions);
            }            
            return $payment;
        });
    }

    public function prescriptionsPaymentSeive(mixed $totalPayments, mixed $prescriptions): void
    {
        $filteredPrescriptions = $prescriptions->reject(function (object $value) {
                    $value->update(['paid' => 0]);
            return $value->qty_billed < 1;
        });

        array_reduce([$filteredPrescriptions], function($carry, $prescription) {

            foreach($prescription as $p){
                $bill = $p->approved ? 0 : $p->hms_bill;

                if ($carry >= $bill){
                    $p->update(['paid' => $p->id == $prescription->last()->id ? $carry : $bill]);
                }
                
                if ($carry < $bill && $carry > 0){
                    $p->update(['paid' => $carry ]);
                }
                
                if ($carry <= 0 && $bill > 0){
                    $p->update(['paid' => 0 ]);
                }

                $carry = $carry - $bill;
            }
            return $carry;

        }, $totalPayments);
    }
    
    public function prescriptionsPaymentSeiveNhis(mixed $totalPayments, mixed $prescriptions): void
    {
        $filteredPrescriptions = $prescriptions->reject(function (object $value) {
                    $value->update(['paid' => 0]);
            return $value->qty_billed < 1;
        });
        array_reduce([$filteredPrescriptions], function($carry, $prescription) {

            foreach($prescription as $p){
                $bill = $p->approved ? $p->nhis_bill : $p->hms_bill;
                
                if ($carry >= $bill){
                    $p->update(['paid' => $p->id == $prescription->last()->id ? $carry : $bill]);
                }

                if ($carry < $bill && $carry > 0){
                    $p->update(['paid' => $carry ]);
                }

                if ($carry <= 0 && $bill > 0){
                    $p->update(['paid' => 0 ]);
                }

                $carry = $carry - $bill;
            }
            return $carry;

        }, $totalPayments);
    }

    public function prescriptionsPaymentSeiveHmo(mixed $totalPayments, mixed $prescriptions): void
    {
        array_reduce([$prescriptions], function($carry, $prescription) {

            foreach($prescription as $p){
                $bill = $p->hmo_bill;
                if ($carry >= $bill){    
                    $p->update(['paid' => $p->id == $prescription->last()->id ? $carry : $bill]);
                }

                if ($carry < $bill && $carry > 0){
                    $p->update(['paid' => $carry ]);
                }

                if ($carry <= 0 && $bill > 0){
                    $p->update(['paid' => 0 ]);
                }

                $carry = $carry - $bill;
            }
            return $carry;

        }, $totalPayments);
    }

    public function prescriptionsPaymentSeiveRetanership(mixed $totalPayments, mixed $prescriptions): void
    {
        array_reduce([$prescriptions], function($carry, $prescription) {

            foreach($prescription as $p){
                $bill = $p->hms_bill;
                if ($carry >= $bill){
                    $p->update(['paid' => $p->id == $prescription->last()->id ? $carry : $bill]);
                }

                if ($carry < $bill && $carry > 0){
                    $p->update(['paid' => $carry ]);
                }

                if ($carry <= 0 && $bill > 0){
                    $p->update(['paid' => 0 ]);
                }

                $carry = $carry - $bill;
            }
            return $carry;

        }, $totalPayments);
    }

    public function destroyPayment(Payment $payment, bool $softDelete, $data)
    {
        return DB::transaction(function () use($payment, $softDelete, $data) {

            //delete payments not associated with a patient or visit
            if (!$payment->patient_id || !$payment->visit_id ){
                return $payment->destroy($payment->id);
            }
            
            //custom soft delete mechanism for non admins
            if ($softDelete){
                $response = $payment->update(['amount_paid' => 0, 'comment' => $data->user()->username . ' removed N'. $payment->amount_paid. ' - ' .$data->deleteReason]);
            } else {
                $response = $payment->destroy($payment->id); //actual delete for admins
            }

            $totalPayments  = $payment->visit->totalPayments();
            $visit          = $payment->visit;

            $visit->update([
                'total_paid'        => $visit->sponsor->category_name == 'HMO' ? $visit->totalPaidPrescriptions() : $totalPayments,
                'total_hms_bill'    => $visit->totalHmsBills(),
                'total_nhis_bill'   => $visit->sponsor->category_name == 'NHIS' ? $visit->totalNhisBills() : 0,
            ]);

            $prescriptions = $visit->prescriptions;

            if ($visit->sponsor->category_name == 'NHIS'){
                $this->prescriptionsPaymentSeiveNhis($totalPayments, $prescriptions);
            } else {
                $this->prescriptionsPaymentSeive($totalPayments, $prescriptions);
            }

            return $response;
        });
    
    }

    public function getCashPaymentsByDate($data)
    {
        $currentDate = new CarbonImmutable();

        if ($data->date){
            return DB::table('payments')
                            ->selectRaw('SUM(payments.amount_paid) as totalCash, pay_methods.id as id')
                            ->leftJoin('pay_methods', 'payments.pay_method_id', '=', 'pay_methods.id')
                            ->where('pay_methods.name', 'Cash')
                            ->groupBy('id')
                            ->whereDate('payments.created_at', $data->date)
                            ->first();
        }

        return DB::table('payments')
                            ->selectRaw('SUM(payments.amount_paid) as totalCash, pay_methods.id as id')
                            ->leftJoin('pay_methods', 'payments.pay_method_id', '=', 'pay_methods.id')
                            ->where('pay_methods.name', 'Cash')
                            ->groupBy('id')
                            ->whereDate('payments.created_at',  $currentDate->format('Y-m-d'))
                            ->first();
    }

    public function totalYearlyIncomeFromCashPatients($data)
    {
        $currentDate = new Carbon();

        if ($data->year){

            return DB::table('payments')
                            ->selectRaw('SUM(amount_paid) as cashPaid, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                            ->whereYear('created_at', $data->year)
                            ->groupBy('month_name', 'month')
                            ->orderBy('month')
                            ->get();
        }

        return DB::table('payments')
                        ->selectRaw('SUM(amount_paid) as cashPaid, MONTH(created_at) as month, MONTHNAME(created_at) as month_name')
                        ->whereYear('created_at', $currentDate->year)
                        ->groupBy('month_name', 'month')
                        ->orderBy('month')
                        ->get();
    }
}