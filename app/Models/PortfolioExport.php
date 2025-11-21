<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioExport extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'format',
        'status',
        'file_path',
        'file_size',
        'export_options',
        'error_message',
        'completed_at',
    ];

    protected $casts = [
        'export_options' => 'json',
        'completed_at' => 'datetime',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}