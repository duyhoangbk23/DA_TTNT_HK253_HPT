<?php

namespace Database\Seeders;

use App\Models\DeviceDashboardData;
use App\Models\Device;
use Illuminate\Database\Seeder;

class DeviceDashboardDataSeeder extends Seeder
{
    public function run(): void
    {
        $device = Device::first();
        if (!$device) return;

        $points = 24;
        $wave = function ($base, $amp, $shift) use ($points) {
            $result = [];
            for ($x = 0; $x < $points; $x++) {
                $result[] = round($base + $amp * sin(($x + $shift) / 2), 2);
            }
            return $result;
        };

        $tdsWave = $wave(52, 8, 1);
        $tempWave = $wave(28, 3, 2);
        $flowWave = $wave(170, 25, 0);
        $phWave = $wave(72, 4, 3);

        for ($i = 0; $i < $points; $i++) {
            DeviceDashboardData::insertOrIgnore([
                'device_id' => $device->id,
                'recorded_at' => now()->subHours($points - $i - 1),
                'tds' => $tdsWave[$i],
                'temperature' => $tempWave[$i],
                'water_flow' => $flowWave[$i],
                'ph' => $phWave[$i] / 10,
            ]);
        }
    }
}
