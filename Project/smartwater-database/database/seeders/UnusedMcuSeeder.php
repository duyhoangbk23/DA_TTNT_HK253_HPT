<?php

namespace Database\Seeders;

use App\Models\Mcu;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;

class UnusedMcuSeeder extends Seeder
{
    public function run(): void
    {
        $firmwares = ['v1.0.0', 'v1.1.0', 'v1.2.0', 'v2.0.0', 'v2.1.0'];
        $statuses = ['offline', 'offline', 'offline', 'online', 'error'];

        for ($i = 0; $i < 12; $i++) {
            Mcu::create([
                'mcu_code' => sprintf('ESP32_%03d', 31 + $i),
                'serial_number' => sprintf('SN-%06d', 31 + $i),
                'firmware_version' => $firmwares[array_rand($firmwares)],
                'api_key' => Str::random(40),
                'status' => $statuses[$i % count($statuses)],
                'last_connected_at' => null,
            ]);
        }
    }
}
