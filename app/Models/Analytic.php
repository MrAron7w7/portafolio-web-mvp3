<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Analytic extends Model
{
    use HasFactory;

    protected $fillable = [
        'portfolio_id',
        'date',
        'views',
        'unique_visitors',
        'referrer',
        'country',
        'device_type',
        'browser',
        'page_views',
    ];

    protected $casts = [
        'date' => 'date',
        'page_views' => 'json',
    ];

    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }
}