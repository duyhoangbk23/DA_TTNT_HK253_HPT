<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;

class DashboardController extends Controller
{
    protected $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function index()
    {
        return view('dashboard.index', [
            'kpis'              => $this->dashboardService->getKpis(),
            'deviceStatus'      => $this->dashboardService->getDeviceStatusBreakdown(),
            'customersMonth'    => $this->dashboardService->getCustomersByMonth(),
            'maintenanceMonth'  => $this->dashboardService->getMaintenanceByMonth(),
            'recentActivity'    => $this->dashboardService->getRecentActivity(),
            'recentMaint'       => $this->dashboardService->getRecentMaintenance(),
            'expiringContracts' => $this->dashboardService->getExpiringContracts(),
        ]);
    }
}
