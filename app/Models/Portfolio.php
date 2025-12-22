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
        'access_mode',
        'link_permission',
        'link_audience_type',
        'share_token',
        'link_created_at',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'is_active' => 'boolean',
        'template_data' => 'json',
        'theme_settings' => 'json',
        'seo_metadata' => 'json',
        'published_at' => 'datetime',
        'link_created_at' => 'datetime',
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

    public function communityPost()
    {
        return $this->hasOne(CommunityPost::class);
    }

    /**
     * Relación con correos autorizados
     */
    public function authorizedEmails(): HasMany
    {
        return $this->hasMany(PortfolioAuthorizedEmail::class);
    }

    // ========================================
    // LÓGICA DEL SISTEMA DE COMPARTIR
    // ========================================

    /**
     * Generar un enlace compartido seguro (UUID)
     */
    public function generateShareLink(): string
    {
        $this->update([
            'access_mode' => 'link',
            'share_token' => \Illuminate\Support\Str::random(32),
            'link_created_at' => now(),
            'link_permission' => 'view',
            'link_audience_type' => 'any_with_link',
            'is_public' => true,
        ]);

        return $this->getShareUrl();
    }

    /**
     * Desactivar enlace compartido
     */
    public function disableShareLink(): void
    {
        $this->update([
            'access_mode' => 'owner_only',
            'share_token' => null,
            'link_created_at' => null,
        ]);
    }

    /**
     * Obtener la URL completa del enlace compartido
     */
    public function getShareUrl(): ?string
    {
        if (!$this->share_token) return null;
        return url("/share/{$this->share_token}");
    }

    /**
     * Verificar si tiene un enlace activo
     */
    public function hasActiveLink(): bool
    {
        return $this->access_mode === 'link' && !empty($this->share_token);
    }

    /**
     * Cambiar permisos del enlace
     */
    public function setLinkPermission(string $permission): void
    {
        if (in_array($permission, ['view', 'view_edit'])) {
            $this->update(['link_permission' => $permission]);
        }
    }

    /**
     * Cambiar tipo de audiencia
     */
    public function setLinkAudienceType(string $type): void
    {
        if (in_array($type, ['any_with_link', 'emails_only'])) {
            $this->update(['link_audience_type' => $type]);
        }
    }

    /**
     * Agregar correo autorizado
     */
    public function addAuthorizedEmail(string $email, ?string $invitedByName = null): PortfolioAuthorizedEmail
    {
        return $this->authorizedEmails()->updateOrCreate(
            ['email' => strtolower(trim($email))],
            ['invited_by_name' => $invitedByName, 'invited_at' => now()]
        );
    }

    /**
     * Eliminar correo autorizado
     */
    public function removeAuthorizedEmail(string $email): bool
    {
        return $this->authorizedEmails()->where('email', strtolower(trim($email)))->delete() > 0;
    }

    /**
     * Verificar si el acceso requiere correo autorizado
     */
    public function requiresAuthorizedEmail(): bool
    {
        return $this->access_mode === 'link' && $this->link_audience_type === 'emails_only';
    }

    /**
     * Lógica central de autorización via enlace
     */
    public function canAccessViaLink(?string $userEmail = null): array
    {
        // 1. Si está en owner_only, nadie excepto el dueño entra (el dueño entraría por la ruta de dashboard, no por aquí)
        if ($this->access_mode === 'owner_only') {
            return ['allowed' => false, 'reason' => 'private'];
        }

        // 2. Si es acceso para cualquier persona con el enlace
        if ($this->link_audience_type === 'any_with_link') {
            return ['allowed' => true, 'permission' => $this->link_permission];
        }

        // 3. Si es restringido por correo
        if ($this->link_audience_type === 'emails_only') {
            if (!$userEmail) {
                return ['allowed' => false, 'reason' => 'auth_required'];
            }

            $authorized = $this->authorizedEmails()->forEmail($userEmail)->first();
            
            if ($authorized) {
                $authorized->markAsAccessed();
                return ['allowed' => true, 'permission' => $this->link_permission];
            }

            return ['allowed' => false, 'reason' => 'not_authorized'];
        }

        return ['allowed' => false, 'reason' => 'unknown'];
    }
}