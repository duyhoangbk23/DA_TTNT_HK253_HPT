<?php

namespace App\Services;

use App\Models\Telemetry;
use Illuminate\Pagination\LengthAwarePaginator;

class DeviceTelemetryService
{
    // mcu_id là định danh chuỗi xuyên suốt firmware, telemetry và thiết bị; không chuyển sang khóa số nội bộ mcus.id.
    public function forMcu(?string $mcuId): array
    {
        $mcuId = trim((string) $mcuId);
        if ($mcuId === '') {
            return $this->emptySeries();
        }

        // Biểu đồ dùng tối đa 500 bản ghi mới nhất rồi sắp lại theo thời gian tăng dần để hiển thị.
        $records = Telemetry::query()
            ->where('mcu_id', $mcuId)
            ->latest('timestamp')
            ->latest('id')
            ->limit(500)
            ->get()
            ->sortBy('timestamp')
            ->values();

        return [
            'labels' => $records->map(fn (Telemetry $row) => $row->timestamp->format('H:i'))->all(),
            'tds' => $records->pluck('tds')->all(),
            'alerts' => $records->map(fn (Telemetry $row) => [
                'time' => $row->timestamp->format('H:i'),
                'alert' => $row->alert ?: 'normal',
                'tds' => $row->tds,
            ])->all(),
        ];
    }

    // Log dùng paginator riêng để không làm đổi dữ liệu biểu đồ.
    public function paginatedLogsForMcu(?string $mcuId, int $page = 1, int $perPage = 15): LengthAwarePaginator
    {
        $mcuId = trim((string) $mcuId);
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));

        return Telemetry::query()
            ->where('mcu_id', $mcuId)
            ->latest('timestamp')
            ->latest('id')
            ->paginate($perPage, ['*'], 'telemetry_page', $page);
    }

    private function emptySeries(): array
    {
        return ['labels' => [], 'tds' => [], 'alerts' => []];
    }
}
