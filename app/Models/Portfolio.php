<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string|null $description
 * @property string $template_type
 * @property array|null $template_data
 * @property bool $is_public
 * @property bool $is_active
 * @property bool $is_completed
 * @property string|null $custom_domain
 * @property array|null $theme_settings
 * @property array|null $seo_metadata
 * @property \Carbon\Carbon|null $published_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\User $user
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\PortfolioSection[] $sections
 */
class Portfolio extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'template_type',
        'template_data',
        'is_new',
        'is_completed',
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