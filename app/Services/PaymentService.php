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
            $payment = $user->payments->create([
                'amount_paid'   => $data->amount,
                'pay_method'    => $data->payMethod,
                'comment'       => $data->comment,
                'patient_id'    => $data->patientId,
                'visit_id'      => $data->visitId,
            ]);

            $totalPayments = $payment->visitPayments($payment->visit);

            $payment->visit->update([
                'total_paid' => $totalPayments
            ]);

            $totalPaymentsforPrescriptions = $totalPayments;

            $prescriptions = $payment->visit->prescriptions;

            array_reduce($prescriptions, function($carry, $prescription) {
                $carry = $carry - $prescription->hms_bill;
                $carry >= $prescription->hms_bill ? 
                $prescription->update(['paid' => $prescription->hms_bill]) :
                '';
                return $carry;
            }, $totalPaymentsforPrescriptions);
            return $payment;
        });
    }
}