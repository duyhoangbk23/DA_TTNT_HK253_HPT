<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

final class MaintenanceWorkOrderService
{
    private const LEAD_DAYS = 7;

    // Đồng bộ lịch chỉ xét hợp đồng còn hiệu lực và thiết bị chưa thay thế.
    public function synchronizeScheduled(Carbon $today): int
    {
        $created = 0;
        $contracts = DB::table('contracts')
            ->where('status', 'active')
            ->whereNotNull('install_date')
            ->whereNotNull('maintenance_cycle_months')
            ->get();

        foreach ($contracts as $contract) {
            $dueDates = $this->scheduleDates($contract, $today);
            if ($dueDates === []) {
                continue;
            }

            $devices = DB::table('devices')
                ->where('contract_id', $contract->id)
                ->whereNull('replaced_at')
                ->get();

            foreach ($devices as $device) {
                foreach ($dueDates as $dueDate) {
                    // Khóa chống trùng theo thiết bị, loại scheduled và ngày đến hạn.
                    if ($this->hasOrderForSchedule($device->id, $dueDate)) {
                        continue;
                    }

                    $inserted = DB::table('maintenance_work_orders')->insertOrIgnore([
                        'device_id' => $device->id,
                        'contract_id' => $contract->id,
                        'type' => 'scheduled',
                        'source_alert' => null,
                        'priority' => 'normal',
                        'status' => 'new',
                        'scheduled_for' => $dueDate->toDateString(),
                        'triggered_at' => null,
                        'telemetry_snapshot' => null,
                        'open_key' => "scheduled:{$device->id}",
                        'created_at' => $today->toDateTimeString(),
                        'updated_at' => $today->toDateTimeString(),
                    ]);
                    $created += $inserted;

                    break;
                }
            }
        }

        return $created;
    }

    // Alert tạo lịch tức thì từ telemetry bất thường mới nhất của mỗi MCU.
    public function synchronizeAlerts(Carbon $now): int
    {
        $created = 0;
        $latestByMcu = DB::table('telemetry')
            ->whereNotNull('alert')
            ->where('timestamp', '<=', $now->toDateTimeString())
            ->orderByDesc('timestamp')
            ->orderByDesc('id')
            ->get()
            ->unique('mcu_id');

        foreach ($latestByMcu as $telemetry) {
            $alert = strtolower(trim((string) $telemetry->alert));
            if (in_array($alert, ['', 'normal', 'online'], true)) {
                continue;
            }

            $device = DB::table('devices')
                ->where('mcu_id', $telemetry->mcu_id)
                ->whereNull('replaced_at')
                ->first();
            // Khóa chống trùng ngăn cùng một alert đã xử lý sinh thêm ticket khi telemetry đến liên tục.
            if ($device === null || $this->hasHandledAlert($device->id, $alert, $telemetry->timestamp)) {
                continue;
            }

            $openKey = "alert:{$device->id}:{$alert}";
            $snapshot = json_encode([
                'timestamp' => $telemetry->timestamp,
                'mcu_id' => $telemetry->mcu_id,
                'tds' => $telemetry->tds,
                'alert' => $alert,
            ], JSON_THROW_ON_ERROR);

            $openOrder = DB::table('maintenance_work_orders')->where('open_key', $openKey)->first();
            // Một alert còn mở được cập nhật snapshot thay vì tạo ticket song song.
            if ($openOrder !== null) {
                DB::table('maintenance_work_orders')->where('id', $openOrder->id)->update([
                    'triggered_at' => $telemetry->timestamp,
                    'telemetry_snapshot' => $snapshot,
                    'updated_at' => $now->toDateTimeString(),
                ]);
                continue;
            }

            $inserted = DB::table('maintenance_work_orders')->insertOrIgnore([
                'device_id' => $device->id,
                'contract_id' => $device->contract_id,
                'type' => 'alert',
                'source_alert' => $alert,
                'priority' => $this->priorityFor($alert),
                'status' => 'new',
                'scheduled_for' => null,
                'triggered_at' => $telemetry->timestamp,
                'telemetry_snapshot' => $snapshot,
                'open_key' => $openKey,
                'created_at' => $now->toDateTimeString(),
                'updated_at' => $now->toDateTimeString(),
            ]);
            $created += $inserted;
        }

        return $created;
    }

    // Hoàn tất ticket giải phóng open_key để alert hoặc lịch hợp lệ về sau có thể tạo ticket mới.
    public function completeWorkOrder(int $workOrderId, Carbon $completedAt): void
    {
        DB::table('maintenance_work_orders')
            ->where('id', $workOrderId)
            ->update([
                'status' => 'completed',
                'open_key' => null,
                'completed_at' => $completedAt->toDateTimeString(),
                'updated_at' => $completedAt->toDateTimeString(),
            ]);
    }

    // Lịch định kỳ bắt đầu từ ngày hiệu lực hợp đồng và tăng theo maintenance_cycle_months; không lấy ngày bảo trì gần nhất làm mốc.
    private function scheduleDates(object $contract, Carbon $today): array
    {
        $cycleMonths = (int) $contract->maintenance_cycle_months;
        if ($cycleMonths < 1) {
            return [];
        }

        $endDate = Carbon::parse($contract->end_date)->endOfDay();
        $nextDate = Carbon::parse($contract->install_date)->addMonthsNoOverflow($cycleMonths)->startOfDay();
        $lastDueDate = null;

        while ($nextDate->lte($today->copy()->endOfDay()) && $nextDate->lte($endDate)) {
            $lastDueDate = $nextDate->copy();
            $nextDate->addMonthsNoOverflow($cycleMonths);
        }

        $dates = $lastDueDate === null ? [] : [$lastDueDate];
        if ($nextDate->lte($today->copy()->addDays(self::LEAD_DAYS)->endOfDay()) && $nextDate->lte($endDate)) {
            $dates[] = $nextDate;
        }

        return $dates;
    }

    // Kiểm tra trùng lịch theo ngày đến hạn cố định của hợp đồng.
    private function hasOrderForSchedule(int $deviceId, Carbon $dueDate): bool
    {
        return DB::table('maintenance_work_orders')
            ->where('device_id', $deviceId)
            ->where('type', 'scheduled')
            ->where('scheduled_for', $dueDate->toDateString())
            ->exists();
    }

    // Alert đã được xử lý tại cùng hoặc thời điểm mới hơn không tạo lại ticket.
    private function hasHandledAlert(int $deviceId, string $alert, string $timestamp): bool
    {
        return DB::table('maintenance_work_orders')
            ->where('device_id', $deviceId)
            ->where('type', 'alert')
            ->where('source_alert', $alert)
            ->where('triggered_at', '>=', $timestamp)
            ->exists();
    }

    // Nhóm alert mất kết nối hoặc lỗi nghiêm trọng được ưu tiên critical; các alert còn lại là high.
    private function priorityFor(string $alert): string
    {
        return in_array($alert, ['sensor_disconnected', 'error', 'critical', 'offline'], true)
            ? 'critical'
            : 'high';
    }
}
