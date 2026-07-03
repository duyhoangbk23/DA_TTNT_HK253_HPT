<?php

namespace App\Http\Controllers;

use App\Support\MockData;

class DeviceController extends Controller
{
    public function index()
    {
        $devices = MockData::devices();

        return view('devices.index', [
            'devices'  => $devices,
            'counts'   => [
                'active'      => $devices->where('status', 'active')->count(),
                'maintenance' => $devices->where('status', 'maintenance')->count(),
                'error'       => $devices->where('status', 'error')->count(),
                'pending'     => $devices->where('status', 'pending')->count(),
            ],
        ]);
    }

    public function show(int $id)
    {
        $device = MockData::findDevice($id);
        abort_if(! $device, 404);

        return view('devices.show', [
            'device'      => $device,
            'telemetry'   => MockData::telemetry('24h'),
            'maintenance' => MockData::maintenanceForDevice($id),
            'activities'  => MockData::activities()->take(6),
        ]);
    }
}
