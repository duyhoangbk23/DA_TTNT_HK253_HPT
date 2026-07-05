<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'contract_code',
        'customer_id',
        'contract_type',
        'start_date',
        'install_date',
        'end_date',
        'maintenance_cycle_months',
        'amount',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'install_date' => 'date',
        'end_date' => 'date',
        'amount' => 'integer',
        'maintenance_cycle_months' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(ContractService::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function getExpiringAttribute()
    {
        return $this->end_date && $this->end_date->diffInDays(now()) <= 30 && $this->status === 'active';
    }
}
