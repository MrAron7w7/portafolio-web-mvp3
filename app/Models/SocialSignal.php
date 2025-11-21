<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialSignal extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'platform',
        'signal_type',
        'count',
        'external_id',
        'url',
        'metadata',
        'fetched_at',
    ];

    protected $casts = [
        'metadata' => 'json',
        'fetched_at' => 'datetime',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}