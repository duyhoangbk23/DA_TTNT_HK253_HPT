<?php

namespace App\Console\Commands;

use App\Services\MaintenanceWorkOrderService;
use Illuminate\Console\Command;

class GenerateScheduledMaintenanceWorkOrders extends Command
{
    protected $signature = 'maintenance:generate-scheduled';
    protected $description = 'Create due maintenance work orders from active contracts';

    public function handle(MaintenanceWorkOrderService $service): int
    {
        $this->info("Created {$service->synchronizeScheduled(now())} scheduled work order(s).");
        return self::SUCCESS;
    }
}
