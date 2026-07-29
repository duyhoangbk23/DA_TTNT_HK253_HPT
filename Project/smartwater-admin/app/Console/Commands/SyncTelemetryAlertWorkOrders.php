<?php

namespace App\Console\Commands;

use App\Services\MaintenanceWorkOrderService;
use Illuminate\Console\Command;

class SyncTelemetryAlertWorkOrders extends Command
{
    protected $signature = 'maintenance:sync-alerts';
    protected $description = 'Create alert work orders from abnormal telemetry';

    public function handle(MaintenanceWorkOrderService $service): int
    {
        $this->info("Created {$service->synchronizeAlerts(now())} alert work order(s).");
        return self::SUCCESS;
    }
}
