<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.index', [
            'kpis'            => MockData::dashboardKpis(),
            'deviceStatus'    => MockData::deviceStatusBreakdown(),
            'customersMonth'  => MockData::customersByMonth(),
            'maintenanceMonth'=> MockData::maintenanceByMonth(),
            'recentActivity'  => MockData::activities()->take(6),
            'recentMaint'     => MockData::maintenance()->take(5),
            'expiringContracts' => MockData::contracts()->where('expiring_soon', true)->take(5),
        ]);
    }
}
