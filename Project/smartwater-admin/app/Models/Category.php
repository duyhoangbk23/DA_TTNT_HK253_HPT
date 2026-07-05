<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    public $timestamps = true;

    protected $fillable = ['name', 'description', 'status'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
