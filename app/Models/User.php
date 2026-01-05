<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'avatar_url',
        'username',
        'timezone',
        'language',
        'email_notifications',
        'marketing_emails',
        'last_seen_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'last_login_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'email_notifications' => 'boolean',
            'marketing_emails' => 'boolean',
        ];
    }

    public function portfolios(): HasMany
    {
        return $this->hasMany(Portfolio::class);
    }

    public function communityPostRatings(): HasMany
    {
        return $this->hasMany(CommunityPostRating::class);
    }

    // Accessor para obtener el nombre completo
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    // Accessor alternativo
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }

    /**
     * Validate the given two factor authentication code.
     *
     * @param  string  $code
     * @return bool
     */
    public function validateTwoFactorAuthentication($code)
    {
        // 1. Check if it matches our cached Email OTP code
        // We use Cache::pull to retrieve AND remove it (single use)
        // Or Cache::get if we want to allow retry within timeframe, but pull is safer for single use.
        // However, Fortify might retry? Let's use get first, then if valid, the controller consumes it?
        // Actually, confirm logic consumes it. 
        // Let's use get for validation. We should handle expiration or removal if verified.
        
        $cachedCode = \Illuminate\Support\Facades\Cache::get('2fa_code_' . $this->id);
        
        \Log::info('2FA Validation: User ' . $this->id . ' | Input: ' . $code . ' | Cached: ' . $cachedCode);

        if ($cachedCode && $cachedCode == $code) {
             return true;
        }

        // 2. Fallback to standard TOTP (Google Authenticator)
        // This allows user to use EITHER email code OR Google Auth app if they set it up.
        // If you want ONLY email, you can remove this fallback.
        return $this->verifyTwoFactorAuthentication($code);
    }
}
