<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceRecord extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'maintenance_code',
        'device_id',
        'employee_id',
        'maintenance_date',
        'maintenance_type',
        'description',
        'parts_used',
        'cost',
        'status',
    ];

    protected $casts = [
        'maintenance_date' => 'date',
        'cost' => 'integer',
    ];

    protected $appends = ['code'];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function getCodeAttribute()
    {
        return $this->maintenance_code;
    }
}
