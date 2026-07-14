<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'product_code',
        'product_name',
        'category_id',
        'model',
        'capacity',
        'unit',
        'price',
        'image_path',
        'status',
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
    }

    public function batchDetails(): HasMany
    {
        return $this->hasMany(BatchDetail::class);
    }

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }
}
