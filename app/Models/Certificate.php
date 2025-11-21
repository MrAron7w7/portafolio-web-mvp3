<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'title',
        'issuer',
        'credential_id',
        'credential_url',
        'issued_at',
        'expires_at',
        'description',
        'certificate_image',
        'does_not_expire',
    ];

    protected $casts = [
        'issued_at' => 'date',
        'expires_at' => 'date',
        'does_not_expire' => 'boolean',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}