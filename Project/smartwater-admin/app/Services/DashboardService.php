<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Device;
use App\Models\Contract;
use App\Models\MaintenanceRecord;
use App\Models\ActivityLog;

class DashboardService
{
    public function getKpis()
    {
        $totalCustomers = Customer::count();
        $totalProducts = Product::count();
        $totalDevices = Device::count();
        $activeDevices = Device::where('status', 'active')->count();
        $maintenanceDevices = Device::where('status', 'maintenance')->count();
        $activeContracts = Contract::where('status', 'active')->count();

        return [
            [
                'label' => 'Tổng khách hàng',
                'value' => $totalCustomers,
                'icon' => 'bi-people',
                'color' => 'primary',
                'trend' => '+12%',
                'up' => true,
            ],
            [
                'label' => 'Tổng sản phẩm',
                'value' => $totalProducts,
                'icon' => 'bi-box-seam',
                'color' => 'success',
                'trend' => '+5%',
                'up' => true,
            ],
            [
                'label' => 'Tổng thiết bị',
                'value' => $totalDevices,
                'icon' => 'bi-gear',
                'color' => 'info',
                'trend' => '+8%',
                'up' => true,
            ],
            [
                'label' => 'Thiết bị hoạt động',
                'value' => $activeDevices,
                'icon' => 'bi-check-circle',
                'color' => 'success',
                'trend' => '+3%',
                'up' => true,
            ],
            [
                'label' => 'Thiết bị bảo trì',
                'value' => $maintenanceDevices,
                'icon' => 'bi-tools',
                'color' => 'warning',
                'trend' => '0%',
                'up' => false,
            ],
            [
                'label' => 'Hợp đồng hoạt động',
                'value' => $activeContracts,
                'icon' => 'bi-file-earmark-text',
                'color' => 'primary',
                'trend' => '+6%',
                'up' => true,
            ],
        ];
    }

    public function getDeviceStatusBreakdown()
    {
        $statuses = Device::groupBy('status')
            ->selectRaw('status, COUNT(*) as count')
            ->pluck('count', 'status');

        return [
            'labels' => ['Hoạt động', 'Bảo trì', 'Lỗi', 'Chờ lắp đặt'],
            'series' => [
                $statuses['active'] ?? 0,
                $statuses['maintenance'] ?? 0,
                $statuses['error'] ?? 0,
                $statuses['pending'] ?? 0,
            ],
        ];
    }

    public function getCustomersByMonth()
    {
        $months = [];
        $series = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = "T$i";
            $count = Customer::whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->count();
            $series[] = $count;
        }

        return [
            'labels' => $months,
            'series' => [$series],
        ];
    }

    public function getMaintenanceByMonth()
    {
        $months = [];
        $series = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = "T$i";
            $count = MaintenanceRecord::whereMonth('created_at', $i)
                ->whereYear('created_at', now()->year)
                ->count();
            $series[] = $count;
        }

        return [
            'labels' => $months,
            'series' => [$series],
        ];
    }

    public function getRecentActivity($limit = 6)
    {
        return ActivityLog::latest()
            ->limit($limit)
            ->get()
            ->map(fn ($log) => [
                'time' => $log->created_at->format('H:i'),
                'action' => $log->action,
                'user' => $log->user->name ?? 'Unknown',
                'module' => $log->module,
            ])
            ->toArray();
    }

    public function getRecentMaintenance($limit = 5)
    {
        return MaintenanceRecord::latest()
            ->with(['device', 'employee', 'device.customer'])
            ->limit($limit)
            ->get()
            ->map(fn ($m) => [
                'code' => $m->code,
                'customer' => $m->device?->customer?->name ?? '-',
                'employee' => $m->employee->name ?? '-',
                'date' => $m->created_at->format('d/m/Y'),
                'status' => $m->status,
            ])
            ->toArray();
    }

    public function getExpiringContracts($limit = 5)
    {
        return Contract::where('status', 'active')
            ->whereDate('end_date', '<=', now()->addDays(30))
            ->with('customer')
            ->latest('end_date')
            ->limit($limit)
            ->get()
            ->map(fn ($c) => [
                'code' => $c->code,
                'customer' => $c->customer->name ?? '-',
                'end_date' => $c->end_date->format('d/m/Y'),
                'expiring_soon' => true,
            ])
            ->toArray();
    }
}
