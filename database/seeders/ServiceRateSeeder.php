<?php

namespace Database\Seeders;

use App\Models\ServiceRate;
use Illuminate\Database\Seeder;

class ServiceRateSeeder extends Seeder
{
    public function run(): void
    {
        if (ServiceRate::exists()) {
            $this->command->info('Ledger already has unit costs. Skipping initial unit costs seed.');
            return;
        }

        $rates = [
            ['identifier' => 'mtn',     'unit_cost' => 3.00],
            ['identifier' => 'airtel',  'unit_cost' => 3.55],
            ['identifier' => 'glo',     'unit_cost' => 3.55],
            ['identifier' => '9mobile', 'unit_cost' => 3.55],
            ['identifier' => 'other',   'unit_cost' => 4.00],
        ];

        foreach ($rates as $rate) {
            ServiceRate::updateOrCreate(['identifier' => $rate['identifier']], $rate);
        }
    }
}