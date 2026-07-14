<?php

namespace App\Http\Controllers;

use App\Models\Telemetry;
use Illuminate\Http\Request;

class TelemetryController extends Controller
{
    public function ingest(Request $request)
    {
        $input = $request->all();
        if (isset($input['payload']) && is_array($input['payload'])) {
            $input = array_merge($input, $input['payload']);
        }

        $validated = validator($input, [
            'topic' => 'nullable|string|max:255',
            'device_id' => 'required|string|max:100',
            'timestamp' => 'nullable|date_format:Y-m-d H:i:s',
            'tds' => 'nullable|numeric',
            'alert' => 'nullable|string|max:255',
        ])->validate();

        $record = Telemetry::create([
            'timestamp' => $validated['timestamp'] ?? now(),
            'topic' => $validated['topic'] ?? 'devices/telemetry',
            'device_id' => $validated['device_id'],
            'tds' => $validated['tds'] ?? null,
            'alert' => $validated['alert'] ?? null,
        ]);

        return response()->json([
            'status' => 'ok',
            'data' => $record,
        ], 201);
    }
}
