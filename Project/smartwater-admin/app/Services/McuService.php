<?php

namespace App\Services;

use App\Models\Mcu;
use Illuminate\Support\Str;

class McuService
{
    public function getAllMcus()
    {
        return Mcu::all();
    }

    public function getMcuById(int $id)
    {
        return Mcu::findOrFail($id);
    }

    public function getAvailableMcus()
    {
        return Mcu::withCount(['devices as current_device_count' => function ($query) {
            $query->whereNull('replaced_at');
        }])
        ->where(function ($query) {
            $query->where('status', 'online')
                  ->orWhereDoesntHave('devices', function ($query) {
                      $query->whereNull('replaced_at');
                  });
        })
        ->orderBy('mcu_code')
        ->get();
    }

    public function createMcu(array $data): Mcu
    {
        if (($data['status'] ?? '') === '') {
            $data['status'] = null;
        }

        $data['api_key'] = Str::random(40);
        return Mcu::create($data);
    }

    public function updateMcu(int $id, array $data): Mcu
    {
        if (($data['status'] ?? '') === '') {
            $data['status'] = null;
        }

        $mcu = $this->getMcuById($id);
        $mcu->update($data);
        return $mcu;
    }

    public function deleteMcu(int $id): bool
    {
        $mcu = $this->getMcuById($id);

        if ($mcu->devices()->whereNull('replaced_at')->exists()) {
            throw new \Exception('MCU này đang gắn với thiết bị hoạt động, không thể xoá.');
        }

        return $mcu->delete();
    }
}
