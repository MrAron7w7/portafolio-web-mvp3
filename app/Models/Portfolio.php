<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portfolio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'template_data',
        'is_public',
        'is_active',
        'custom_domain',
        'theme_settings',
        'seo_metadata',
        'published_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'template_data' => 'json',
        'theme_settings' => 'json',
        'seo_metadata' => 'json',
        'published_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(PortfolioSection::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(Media::class);
    }

    public function certificates(): HasMany
    {
        return $this->hasMany(Certificate::class);
    }

    public function externalIntegrations(): HasMany
    {
        return $this->hasMany(ExternalIntegration::class);
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(Analytic::class);
    }

    public function socialSignals(): HasMany
    {
        return $this->hasMany(SocialSignal::class);
    }

    public function exports(): HasMany
    {
        return $this->hasMany(PortfolioExport::class);
    }
}