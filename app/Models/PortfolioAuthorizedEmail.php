<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modelo para correos autorizados a acceder a un portafolio
 * 
 * Cuando un portafolio tiene link_audience_type = 'emails_only',
 * solo los correos en esta tabla pueden acceder usando el enlace.
 */
class PortfolioAuthorizedEmail extends Model
{
    protected $fillable = [
        'portfolio_id',
        'email',
        'invited_by_name',
        'invited_at',
        'accessed_at',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'accessed_at' => 'datetime',
    ];

    /**
     * Relación con el portafolio
     */
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    /**
     * Marcar como accedido (primera vez que el invitado usa el enlace)
     */
    public function markAsAccessed(): void
    {
        if (!$this->accessed_at) {
            $this->update(['accessed_at' => now()]);
        }
    }

    /**
     * Scope para buscar por email
     */
    public function scopeForEmail($query, string $email)
    {
        return $query->where('email', strtolower(trim($email)));
    }
}
