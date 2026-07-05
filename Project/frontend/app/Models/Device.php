<?php

namespace App\Models;

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
        'import_date',
        'install_date',
        'firmware_version',
        'location',
        'status',
    ];

    protected $casts = [
        'import_date' => 'date',
        'install_date' => 'date',
    ];

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

    public function dashboardData(): HasMany
    {
        return $this->hasMany(DeviceDashboardData::class);
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}
