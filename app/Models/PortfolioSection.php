<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'component_type',
        'section_name',
        'content',
        'order',
        'is_visible',
        'layout_config',
    ];

    protected $casts = [
        'content' => 'json',
        'is_visible' => 'boolean',
        'layout_config' => 'json',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}