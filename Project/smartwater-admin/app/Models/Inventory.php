<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'quantity',
        'reserved_quantity',
        'unit_cost',
        'last_updated',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reserved_quantity' => 'integer',
        'unit_cost' => 'integer',
        'last_updated' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getAvailableAttribute()
    {
        return max($this->quantity - $this->reserved_quantity, 0);
    }

    public function getStockStatusAttribute()
    {
        if ($this->quantity === 0) {
            return 'out';
        }
        if ($this->quantity <= 10) {
            return 'low';
        }
        return 'ok';
    }
}
