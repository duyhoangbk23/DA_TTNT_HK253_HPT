<?php

namespace Database\Seeders;

use App\Models\Mcu;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class McuSeeder extends Seeder
{
    public function run(): void
    {
        $firmwares = ['v1.0.0', 'v1.1.0', 'v1.2.0', 'v2.0.0', 'v2.1.0'];

        for ($i = 1; $i <= 30; $i++) {
            Mcu::create([
                'mcu_id' => 'ESP32_' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'serial_number' => 'SN-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'firmware_version' => $firmwares[array_rand($firmwares)],
                'api_key' => Str::random(40),
                'status' => rand(0, 3) < 2 ? 'online' : (rand(0, 1) ? 'offline' : 'error'),
                'last_connected_at' => $i <= 20 ? now()->subHours(rand(1, 24)) : null,
            ]);
        }
    }
}
