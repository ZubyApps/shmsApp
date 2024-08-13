<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

Class PaymentService
{
    public function __construct(private readonly Payment $payment)
    {
        
    }

    public function create(Request $data, User $user): Payment
    {
        return DB::transaction(function () use($data, $user) {
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

    // public function prescriptionsPaymentSeive(mixed $totalPayments, mixed $prescriptions): void
    // {
    //     array_reduce([$prescriptions], function($carry, $prescription) {
    //         foreach($prescription as $p){
    //             $bill = $p->approved ? 0 : $p->hms_bill;
    //             $p->update(['paid' => $carry >= $bill ? $bill : ($carry < $bill && $carry > 0 ? $carry : 0)]);
    //             $carry = $carry - $bill;
    //         }
    //         return $carry;

    //     }, $totalPayments);
    // }

    public function prescriptionsPaymentSeive(mixed $totalPayments, mixed $prescriptions): void
    {
        array_reduce([$prescriptions], function($carry, $prescription) {
            foreach($prescription as $p){
                $bill = $p->approved ? 0 : $p->hms_bill;
                $paid = $p->paid;
                
                if ($carry >= $bill){
                    $p->update(['paid' => $bill == 0 && $p->qty_billed > 0 ? $paid : $bill]);
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

    // public function prescriptionsPaymentSeiveNhis(mixed $totalPayments, mixed $prescriptions): void
    // {
    //     array_reduce([$prescriptions], function($carry, $prescription) {
    //         foreach($prescription as $p){
    //             $bill = $p->approved ? $p->nhis_bill : $p->hms_bill;
    //             $p->update(['paid' => $carry >= $bill ? $bill : ($carry < $bill && $carry > 0 ? $carry : 0)]);
    //             $carry = $carry - $bill;
    //         }
    //         return $carry;

    //     }, $totalPayments);
    // }
    
    public function prescriptionsPaymentSeiveNhis(mixed $totalPayments, mixed $prescriptions): void
    {
        array_reduce([$prescriptions], function($carry, $prescription) {
            foreach($prescription as $p){
                $bill = $p->approved ? $p->nhis_bill : $p->hms_bill;
                $paid = $p->paid;
                
                if ($carry >= $bill){
                    $p->update(['paid' => $bill == 0 && $p->qty_billed > 0 ? $paid : $bill]);
                }

                if ($carry < $bill && $carry > 0){
                    $p->update(['paid' => $carry + $paid]);
                }

                if ($carry <= 0 && $bill > 0){
                    $p->update(['paid' => 0 ]);
                }

                $carry = $carry - $bill;
            }
            return $carry;

        }, $totalPayments);
    }

    public function destroyPayment(Payment $payment)
    {
        return DB::transaction(function () use($payment) {
            $deleted = $payment->destroy($payment->id);

            $totalPayments  = $payment->visit->totalPayments();
            $visit          = $payment->visit;

            $payment->visit->update([
                'total_paid'        => $visit->sponsor->category_name == 'HMO' ? $visit->totalPaidPrescriptions() : $totalPayments,
                'total_hms_bill'    => $visit->totalHmsBills(),
                'total_nhis_bill'   => $visit->sponsor->category_name == 'NHIS' ? $visit->totalNhisBills() : 0,
            ]);

            $prescriptions = $payment->visit->prescriptions;

            if ($payment->visit->sponsor->category_name == 'NHIS'){
                $this->prescriptionsPaymentSeiveNhis($totalPayments, $prescriptions);
            } else {
                $this->prescriptionsPaymentSeive($totalPayments, $prescriptions);
            }

            return $deleted;
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
}