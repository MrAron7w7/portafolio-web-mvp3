<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property int $portfolio_id
 * @property \Illuminate\Support\Carbon $date
 * @property int $views
 * @property int $unique_visitors
 * @property string|null $referrer
 * @property string|null $country
 * @property string|null $device_type
 * @property string|null $browser
 * @property array<array-key, mixed>|null $page_views
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Portfolio $portfolio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereBrowser($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereDeviceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic wherePageViews($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereReferrer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereUniqueVisitors($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Analytic whereViews($value)
 */
	class Analytic extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $portfolio_id
 * @property string $title
 * @property string $issuer
 * @property string|null $credential_id
 * @property string|null $credential_url
 * @property \Illuminate\Support\Carbon $issued_at
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property string|null $description
 * @property string|null $certificate_image
 * @property bool $does_not_expire
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Portfolio $portfolio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCertificateImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCredentialId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereCredentialUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereDoesNotExpire($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereIssuedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereIssuer($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Certificate whereUpdatedAt($value)
 */
	class Certificate extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $community_post_id
 * @property int|null $parent_id
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read CommunityComment|null $parent
 * @property-read \App\Models\CommunityPost $post
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CommunityComment> $replies
 * @property-read int|null $replies_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereCommunityPostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityComment whereUserId($value)
 */
	class CommunityComment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $user_id
 * @property int $portfolio_id
 * @property string $title
 * @property string $content
 * @property int $views_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityComment> $comments
 * @property-read int|null $comments_count
 * @property-read \App\Models\Portfolio $portfolio
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityPostRating> $ratings
 * @property-read int|null $ratings_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPost whereViewsCount($value)
 */
	class CommunityPost extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $community_post_id
 * @property int $user_id
 * @property int $rating
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\CommunityPost $post
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostRating newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostRating newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostRating query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostRating whereCommunityPostId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostRating whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostRating whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostRating whereRating($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostRating whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CommunityPostRating whereUserId($value)
 */
	class CommunityPostRating extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $portfolio_id
 * @property string $service
 * @property string|null $access_token
 * @property string|null $refresh_token
 * @property \Illuminate\Support\Carbon|null $token_expires_at
 * @property array<array-key, mixed>|null $configuration
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_sync_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Portfolio $portfolio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereAccessToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereConfiguration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereLastSyncAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereRefreshToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereService($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereTokenExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ExternalIntegration whereUpdatedAt($value)
 */
	class ExternalIntegration extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $key
 * @property array<array-key, mixed> $content
 * @property array<array-key, mixed>|null $images
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingPageSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingPageSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingPageSection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingPageSection whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingPageSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingPageSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingPageSection whereImages($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingPageSection whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LandingPageSection whereUpdatedAt($value)
 */
	class LandingPageSection extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $portfolio_id
 * @property string $type
 * @property string $file_name
 * @property string $file_path
 * @property string $mime_type
 * @property int $size
 * @property string $disk
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Portfolio $portfolio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereDisk($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media whereUpdatedAt($value)
 */
	class Media extends \Eloquent {}
}

namespace App\Models{
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
 * @property int $is_new
 * @property string $access_mode
 * @property string $link_permission
 * @property string $link_audience_type
 * @property string|null $share_token
 * @property \Illuminate\Support\Carbon|null $link_created_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Analytic> $analytics
 * @property-read int|null $analytics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PortfolioAuthorizedEmail> $authorizedEmails
 * @property-read int|null $authorized_emails_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Certificate> $certificates
 * @property-read int|null $certificates_count
 * @property-read \App\Models\CommunityPost|null $communityPost
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PortfolioExport> $exports
 * @property-read int|null $exports_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ExternalIntegration> $externalIntegrations
 * @property-read int|null $external_integrations_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Media> $media
 * @property-read int|null $media_count
 * @property-read int|null $sections_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SocialSignal> $socialSignals
 * @property-read int|null $social_signals_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereAccessMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereCustomDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereIsCompleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereIsNew($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereIsPublic($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereLinkAudienceType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereLinkCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereLinkPermission($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio wherePublishedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereSeoMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereShareToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereTemplateData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereTemplateType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereThemeSettings($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Portfolio withoutTrashed()
 */
	class Portfolio extends \Eloquent {}
}

namespace App\Models{
/**
 * Modelo para correos autorizados a acceder a un portafolio
 * 
 * Cuando un portafolio tiene link_audience_type = 'emails_only',
 * solo los correos en esta tabla pueden acceder usando el enlace.
 *
 * @property int $id
 * @property int $portfolio_id
 * @property string $email
 * @property string|null $invited_by_name
 * @property \Illuminate\Support\Carbon $invited_at
 * @property \Illuminate\Support\Carbon|null $accessed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Portfolio $portfolio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail forEmail(string $email)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail whereAccessedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail whereInvitedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail whereInvitedByName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioAuthorizedEmail whereUpdatedAt($value)
 */
	class PortfolioAuthorizedEmail extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $portfolio_id
 * @property string $format
 * @property string $status
 * @property string|null $file_path
 * @property int|null $file_size
 * @property array<array-key, mixed>|null $export_options
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $completed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Portfolio $portfolio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereCompletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereErrorMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereExportOptions($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereFilePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereFileSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereFormat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioExport whereUpdatedAt($value)
 */
	class PortfolioExport extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $portfolio_id
 * @property string $component_type
 * @property string $section_name
 * @property array<array-key, mixed>|null $content
 * @property int $order
 * @property bool $is_visible
 * @property array<array-key, mixed>|null $layout_config
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Portfolio $portfolio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection whereComponentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection whereIsVisible($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection whereLayoutConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection whereSectionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PortfolioSection whereUpdatedAt($value)
 */
	class PortfolioSection extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $portfolio_id
 * @property string $platform
 * @property string $signal_type
 * @property int $count
 * @property string|null $external_id
 * @property string|null $url
 * @property array<array-key, mixed>|null $metadata
 * @property \Illuminate\Support\Carbon $fetched_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Portfolio $portfolio
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal whereCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal whereExternalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal whereFetchedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal whereMetadata($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal wherePlatform($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal wherePortfolioId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal whereSignalType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SocialSignal whereUrl($value)
 */
	class SocialSignal extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property \Illuminate\Support\Carbon|null $two_factor_confirmed_at
 * @property string|null $avatar_url
 * @property string $timezone
 * @property string $language
 * @property bool $email_notifications
 * @property bool $marketing_emails
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $last_login_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $last_seen_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CommunityPostRating> $communityPostRatings
 * @property-read int|null $community_post_ratings_count
 * @property-read string $full_name
 * @property-read string $name
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Portfolio> $portfolios
 * @property-read int|null $portfolios_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereAvatarUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailNotifications($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLanguage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastLoginAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereMarketingEmails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereTwoFactorSecret($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent implements \Illuminate\Contracts\Auth\MustVerifyEmail {}
}

