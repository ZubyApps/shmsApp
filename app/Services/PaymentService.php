<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\Payment;
use App\Models\User;
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
            $payment = $user->payments()->create([
                'amount_paid'   => $data->amount,
                'pay_method'    => $data->payMethod,
                'comment'       => $data->comment,
                'patient_id'    => $data->patientId,
                'visit_id'      => $data->visitId,
            ]);

            $totalPayments = $payment->visit->totalPayments();

            $payment->visit->update([
                'total_paid' => $totalPayments
            ]);

            $prescriptions = $payment->visit->prescriptions;

            if ($payment->visit->sponsor->sponsorCategory->name == 'NHIS'){
                $this->prescriptionsPaymentSeiveNhis($totalPayments, $prescriptions);
            } else {
                $this->prescriptionsPaymentSeive($totalPayments, $prescriptions);
            }            
            return $payment;
        });
    }

    public function prescriptionsPaymentSeive(mixed $totalPayments, mixed $prescriptions): void
    {
        array_reduce([$prescriptions], function($carry, $prescription) {
            foreach($prescription as $p){
                $bill = $p->hms_bill;
                $p->update(['paid' => $carry >= $bill ? $bill : ($carry < $bill && $carry > 0 ? $carry : null)]);
                $carry = $carry - $bill;
            }
            return $carry;

        }, $totalPayments);
    }

    public function prescriptionsPaymentSeiveNhis(mixed $totalPayments, mixed $prescriptions): void
    {
        array_reduce([$prescriptions], function($carry, $prescription) {
            foreach($prescription as $p){
                $bill = $p->hms_bill/10;
                $p->update(['paid' => $carry >= $bill ? $bill : ($carry < $bill && $carry > 0 ? $carry : null)]);
                $carry = $carry - $bill;
            }
            return $carry;

        }, $totalPayments);
    }

    public function destroyPayment(Payment $payment)
    {
        $deleted = $payment->destroy($payment->id);

        $totalPayments = $payment->visit->totalPayments();

        $prescriptions = $payment->visit->prescriptions;

        if ($payment->visit->sponsor->sponsorCategory->name == 'NHIS'){
            $this->prescriptionsPaymentSeiveNhis($totalPayments, $prescriptions);
        } else {
            $this->prescriptionsPaymentSeive($totalPayments, $prescriptions);
        }

        return $deleted;
    
    }
}