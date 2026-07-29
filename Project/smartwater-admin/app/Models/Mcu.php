<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mcu extends Model
{
    use HasFactory;

    public static function isValidSerialNumber(?string $serialNumber): bool
    {
        return is_string($serialNumber) && preg_match('/^SN-\d{6}$/', $serialNumber) === 1;
    }

    public static function getDisplayStatus(?string $status): string
    {
        return match ($status) {
            'online' => 'Online',
            'offline' => 'Offline',
            'error' => 'Error',
            default => 'N/A',
        };
    }

    protected $fillable = [
        'mcu_id',
        'serial_number',
        'firmware_version',
        'api_key',
        'status',
        'last_connected_at',
        'registration_status',
        'connection_status',
        'first_seen_at',
        'last_seen_at',
    ];

    protected $hidden = [
        'api_key',
    ];

    protected function casts(): array
    {
        return [
            'last_connected_at' => 'datetime',
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    // Quan hệ dùng mcu_id chuỗi để giữ đúng định danh xuyên suốt giữa firmware, telemetry và thiết bị.
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'mcu_id', 'mcu_id');
    }

    // MCU đang dùng có một thiết bị chưa thay thế, gắn hợp đồng và có mcu_id.
    public function scopeUsed(Builder $query): Builder
    {
        return $query->whereHas('devices', function (Builder $query) {
            $query->whereNull('replaced_at')
                ->whereNotNull('contract_id')
                ->whereNotNull('mcu_id');
        });
    }

    // MCU chưa dùng không có thiết bị hiện hành đáp ứng điều kiện gắn hợp đồng.
    public function scopeUnused(Builder $query): Builder
    {
        return $query->whereDoesntHave('devices', function (Builder $query) {
            $query->whereNull('replaced_at')
                ->whereNotNull('contract_id')
                ->whereNotNull('mcu_id');
        });
    }

    // Thiết bị hiện hành là liên kết mới nhất còn hiệu lực của MCU này.
    public function currentDevice()
    {
        return $this->devices()
            ->whereNull('replaced_at')
            ->whereNotNull('contract_id')
            ->whereNotNull('mcu_id')
            ->latest()
            ->first();
    }
}
