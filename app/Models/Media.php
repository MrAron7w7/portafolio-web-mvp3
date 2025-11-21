<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'type',
        'file_name',
        'file_path',
        'mime_type',
        'size',
        'disk',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}