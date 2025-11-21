<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExternalIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'service',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'configuration',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'configuration' => 'json',
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}