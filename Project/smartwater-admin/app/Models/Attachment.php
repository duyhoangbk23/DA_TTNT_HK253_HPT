<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attachment extends Model
{
    public $timestamps = true;

    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'mime_type',
        'uploaded_by',
        'related_type',
        'related_id',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function uploadedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
