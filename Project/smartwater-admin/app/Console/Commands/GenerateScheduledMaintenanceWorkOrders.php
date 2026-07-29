<?php

namespace App\Console\Commands;

use App\Services\MaintenanceWorkOrderService;
use Illuminate\Console\Command;

class GenerateScheduledMaintenanceWorkOrders extends Command
{
    protected $signature = 'maintenance:generate-scheduled';
    protected $description = 'Create due maintenance work orders from active contracts';

    // Command chỉ kích hoạt đồng bộ lịch hợp đồng; lịch chạy định kỳ được cấu hình riêng trong routes/console.php.
    public function handle(MaintenanceWorkOrderService $service): int
    {
        $this->info("Created {$service->synchronizeScheduled(now())} scheduled work order(s).");
        return self::SUCCESS;
    }
}
