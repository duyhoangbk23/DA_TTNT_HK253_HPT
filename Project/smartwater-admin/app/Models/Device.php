<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'device_code',
        'serial_number',
        'product_id',
        'customer_id',
        'contract_id',
        'batch_id',
        'mcu_id',
        'import_date',
        'install_date',
        'firmware_version',
        'location',
        'status',
        'replaced_at',
        'replaced_by_device_id',
    ];

    protected $casts = [
        'import_date' => 'date',
        'install_date' => 'date',
        'replaced_at' => 'datetime',
    ];

    public function scopeUsed(Builder $query): Builder
    {
        return $query
            ->whereNull('replaced_at')
            ->whereNotNull('contract_id')
            ->whereNotNull('mcu_id');
    }

    public function scopeUnused(Builder $query): Builder
    {
        return $query
            ->whereNull('replaced_at')
            ->whereNull('contract_id')
            ->whereNull('mcu_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withDefault();
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class)->withDefault();
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class)->withDefault();
    }

    public function mcu(): BelongsTo
    {
        return $this->belongsTo(Mcu::class)->withDefault();
    }

    public function replacedBy(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'replaced_by_device_id');
    }

    public function replaces(): HasMany
    {
        return $this->hasMany(Device::class, 'replaced_by_device_id');
    }

    public function dashboardData(): HasMany
    {
        return $this->hasMany(DeviceDashboardData::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}
