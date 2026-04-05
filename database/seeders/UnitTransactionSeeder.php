<?php

namespace Database\Seeders;

use App\Models\UnitTransaction;
use Illuminate\Database\Seeder;

class UnitTransactionSeeder extends Seeder
{

    public function run(): void
    {
        // If the ledger already has data, stop here.
        if (UnitTransaction::exists()) {
            $this->command->info('Ledger already has a balance. Skipping genesis seed.');
            return;
        }

        UnitTransaction::create([
            'amount'          => 1000.00,
            'running_balance' => 1000.00,
            'type'            => 'deposit',
            'reference'       => 'SYSTEM_GENESIS_CREDIT',
        ]);
    }
}