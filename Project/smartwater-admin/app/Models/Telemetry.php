<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telemetry extends Model
{
    protected $table = 'telemetry';

    protected $fillable = [
        'timestamp',
        'topic',
        'device_id',
        'tds',
        'alert',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
        'tds' => 'float',
    ];
}
