<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class ContractSeeder extends Seeder
{
    public function run(): void
    {
        $types = ['install', 'maintenance', 'replace'];
        $statuses = ['active', 'active', 'active', 'expired', 'cancelled'];
        $customers = Customer::all();

        for ($i = 0; $i < 18; $i++) {
            $cust = $customers[$i % $customers->count()];
            $typeKey = $types[$i % 3];
            $start = now()->subMonths($i + 1);
            $end = (clone $start)->addMonths(24);

            Contract::create([
                'contract_code' => sprintf('HĐ-2025-%03d', $i + 1),
                'customer_id' => $cust->id,
                'contract_type' => $typeKey,
                'start_date' => $start->format('Y-m-d'),
                'install_date' => $start->copy()->addDays(3)->format('Y-m-d'),
                'end_date' => $end->format('Y-m-d'),
                'maintenance_cycle_months' => [3, 6, 12][$i % 3],
                'amount' => (6 + $i % 5) * 1_500_000,
                'status' => $statuses[$i % count($statuses)],
            ]);
        }
    }
}
