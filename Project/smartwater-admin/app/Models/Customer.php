<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $fillable = [
        'customer_code',
        'customer_name',
        'phone',
        'email',
        'address',
        'avatar_path',
        'type',
        'status',
        'joined_at',
    ];

    protected $dates = ['joined_at'];

    public function devices(): HasMany
    {
        return $this->hasMany(Device::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }
}
