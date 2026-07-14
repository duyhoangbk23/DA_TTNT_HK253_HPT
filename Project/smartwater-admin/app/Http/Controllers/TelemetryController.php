<?php

namespace App\Http\Controllers;

use App\Models\Mcu;
use App\Models\DeviceDashboardData;
use Illuminate\Http\Request;

class TelemetryController extends Controller
{
    public function ingest(Request $request)
    {
        $validated = $request->validate([
            'mcu_id' => 'required|string',
            'api_key' => 'required|string',
            'timestamp' => 'nullable|date_format:Y-m-d H:i:s',
            'tds' => 'nullable|numeric',
            'alert' => 'nullable|string|max:255',
        ]);

        // Tìm MCU theo mcu_code
        $mcu = Mcu::where('mcu_code', $validated['mcu_id'])->first();
        if (!$mcu) {
            return response()->json(['error' => 'MCU không tồn tại'], 404);
        }

        // Xác thực API key
        if ($mcu->api_key !== $validated['api_key']) {
            return response()->json(['error' => 'API key không chính xác'], 401);
        }

        // Tìm device đang active gắn với MCU này
        $device = $mcu->currentDevice();
        if (!$device) {
            return response()->json(['error' => 'MCU chưa được lắp vào thiết bị nào'], 404);
        }

        // Tạo telemetry record
        DeviceDashboardData::create([
            'device_id' => $device->id,
            'recorded_at' => $validated['timestamp'] ?? now(),
            'tds' => $validated['tds'] ?? null,
            'alert' => $validated['alert'] ?? null,
        ]);

        // Cập nhật MCU status
        $mcu->update([
            'status' => 'online',
            'last_connected_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }
}
