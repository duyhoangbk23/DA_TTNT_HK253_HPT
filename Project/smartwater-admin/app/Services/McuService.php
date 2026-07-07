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
        return Mcu::whereDoesntHave('devices', function ($query) {
            $query->whereNull('replaced_at');
        })->get();
    }

    public function createMcu(array $data): Mcu
    {
        $data['api_key'] = Str::random(40);
        return Mcu::create($data);
    }

    public function updateMcu(int $id, array $data): Mcu
    {
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
