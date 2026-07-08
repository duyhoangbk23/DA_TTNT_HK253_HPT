<?php

namespace App\Models;

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
        'mcu_code',
        'serial_number',
        'firmware_version',
        'api_key',
        'status',
        'last_connected_at',
    ];

    protected $hidden = [
        'api_key',
    ];

    protected function casts(): array
    {
        return [
            'last_connected_at' => 'datetime',
        ];
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function currentDevice()
    {
        return $this->devices()
            ->whereNull('replaced_at')
            ->latest()
            ->first();
    }
}
