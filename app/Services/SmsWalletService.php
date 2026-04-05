<?php

declare(strict_types = 1);

namespace App\Services;

use App\Models\UnitTransaction;
use App\Models\SmsWalletFunding;
use Illuminate\Support\Facades\DB;

class SmsWalletService
{
    /**
     * FUNDING: Adds units to the global wallet when a purchase is successful.
     * Uses your SmsWalletFunding model to complete the audit trail.
     */
    public function creditWithFunding(SmsWalletFunding $funding, ?string $adminName = null): UnitTransaction
    {
        return DB::transaction(function () use ($funding, $adminName) {
            // 1. Mark the specific funding request as completed
            $funding->update([
                'status'      => SmsWalletFunding::STATUS_PAID,
                'approved_by' => $adminName
            ]);

            // 2. Record the credit in the master ledger
            return $this->adjustBalance(
                (float) $funding->units_added, 
                'credit', 
                "FUND_{$funding->id}"
            );
        });
    }

    /**
     * USAGE: Deducts units from the global wallet when an SMS is sent.
     */
    public function deductForSms(float $cost, string $logId): UnitTransaction
    {
        return $this->adjustBalance(
            -$cost, 
            'usage', 
            "COMM_{$logId}"
        );
    }

    /**
     * CORE: The atomic calculation for the running balance.
     * Keeps your exact column names and 'latest' logic.
     */
    public function adjustBalance(float $amount, string $type, string $reference): UnitTransaction
    {
        return DB::transaction(function () use ($amount, $type, $reference) { 
            
            // Get the current global balance from the last row
            $currentBalance = UnitTransaction::latest('id')->value('running_balance') ?? 0;

            // Create the Ledger entry
            return UnitTransaction::create([
                'amount'          => $amount,
                'running_balance' => $currentBalance + $amount,
                'type'            => $type,
                'reference'       => $reference,
            ]);
        });
    }

    public static function currentBalance(): float
    {
        return (float) (UnitTransaction::latest('id')->value('running_balance') ?? 0.00);
    }
}