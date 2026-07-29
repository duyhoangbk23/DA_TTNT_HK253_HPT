<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaintenanceWorkOrder extends Model
{
    protected $fillable = [
        'device_id', 'contract_id', 'employee_id', 'type', 'source_alert', 'priority', 'status',
        'scheduled_for', 'triggered_at', 'telemetry_snapshot', 'open_key', 'description', 'completed_at',
    ];

    protected $casts = [
        'scheduled_for' => 'date',
        'triggered_at' => 'datetime',
        'telemetry_snapshot' => 'array',
        'completed_at' => 'datetime',
    ];

    // Ticket liên kết thiết bị, hợp đồng và nhân viên để hiển thị và phân công trong luồng bảo trì.
    public function device(): BelongsTo { return $this->belongsTo(Device::class); }
    public function contract(): BelongsTo { return $this->belongsTo(Contract::class); }
    public function employee(): BelongsTo { return $this->belongsTo(Employee::class); }
}
