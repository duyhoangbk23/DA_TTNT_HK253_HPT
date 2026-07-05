<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Batch extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'batch_code',
        'supplier_id',
        'import_date',
        'expiry_date',
        'quantity',
        'note',
    ];

    protected $casts = [
        'import_date' => 'date',
        'expiry_date' => 'date',
        'quantity' => 'integer',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(BatchDetail::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
