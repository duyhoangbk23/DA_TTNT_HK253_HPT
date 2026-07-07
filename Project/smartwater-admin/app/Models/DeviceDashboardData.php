<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceDashboardData extends Model
{
    public const CREATED_AT = null;
    public const UPDATED_AT = null;

    protected $fillable = [
        'device_id',
        'recorded_at',
        'tds',
        'temperature',
        'water_flow',
        'ph',
        'status',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'tds' => 'float',
        'temperature' => 'float',
        'water_flow' => 'float',
        'ph' => 'float',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
