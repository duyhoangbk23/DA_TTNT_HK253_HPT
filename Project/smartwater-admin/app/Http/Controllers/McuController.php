<?php

namespace App\Http\Controllers;

use App\Models\Mcu;
use App\Services\McuService;
use App\Http\Requests\StoreMcuRequest;
use App\Http\Requests\UpdateMcuRequest;

class McuController extends Controller
{
    protected $mcuService;

    public function __construct(McuService $mcuService)
    {
        $this->mcuService = $mcuService;
    }

    public function index()
    {
        $usedMcus = Mcu::withCount(['devices as current_device_count' => function ($query) {
            $query->whereNull('replaced_at');
        }])
        ->whereHas('devices', function ($query) {
            $query->whereNull('replaced_at');
        })
        ->orderBy('mcu_code')
        ->get();

        $unusedMcus = Mcu::withCount(['devices as current_device_count' => function ($query) {
            $query->whereNull('replaced_at');
        }])
        ->whereDoesntHave('devices', function ($query) {
            $query->whereNull('replaced_at');
        })
        ->orderBy('mcu_code')
        ->get();

        return view('mcus.index', [
            'usedMcus' => $usedMcus,
            'unusedMcus' => $unusedMcus,
        ]);
    }

    public function store(StoreMcuRequest $request)
    {
        $data = $request->validated();
        $mcu = $this->mcuService->createMcu($data);
        return back()->with('success', 'MCU đã được tạo thành công. API Key: ' . $mcu->api_key . ' (Lưu API key này ở nơi an toàn)');
    }

    public function update(UpdateMcuRequest $request, Mcu $mcu)
    {
        $data = $request->validated();
        $this->mcuService->updateMcu($mcu->id, $data);
        return back()->with('success', 'MCU đã được cập nhật');
    }

    public function destroy(Mcu $mcu)
    {
        try {
            $this->mcuService->deleteMcu($mcu->id);
            return back()->with('success', 'MCU đã được xoá');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
