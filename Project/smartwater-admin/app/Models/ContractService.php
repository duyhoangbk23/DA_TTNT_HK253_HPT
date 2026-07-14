<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractService extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'contract_id',
        'service_name',
        'service_interval',
        'description',
    ];

    protected $casts = [
        'service_interval' => 'integer',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }
}
