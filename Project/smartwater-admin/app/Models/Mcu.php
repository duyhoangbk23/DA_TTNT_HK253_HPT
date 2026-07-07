<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mcu extends Model
{
    use HasFactory;

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
