<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Publication extends Model
{
    use HasFactory;

    /**
     * Supported social platforms => human label, for the profile form.
     */
    public const SOCIAL_PLATFORMS = [
        'twitter' => 'X / Twitter',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'linkedin' => 'LinkedIn',
        'youtube' => 'YouTube',
        'mastodon' => 'Mastodon',
    ];

    /**
     * The reorderable sections that make up every issue, in canonical order.
     * The header and footer are the same template for every publication; only
     * their order (relative to the content) and the palette are configurable.
     */
    public const STRUCTURE_SECTIONS = ['header', 'content', 'footer'];

    /**
     * Human labels/descriptions for each structure section (for the editor UI).
     *
     * @var array<string, array{label: string, description: string}>
     */
    public const STRUCTURE_LABELS = [
        'header' => ['label' => 'Masthead', 'description' => 'Logo, title, issue number and timeframe.'],
        'content' => ['label' => 'Stories', 'description' => "The issue's articles, in their own order."],
        'footer' => ['label' => 'Footer', 'description' => 'Sign-off, website link and the unsubscribe notice.'],
    ];

    /**
     * Configurable palette colours => human label + default hex value.
     *
     * @var array<string, array{label: string, default: string}>
     */
    public const PALETTE_FIELDS = [
        'header_bg' => ['label' => 'Header background', 'default' => '#16151a'],
        'header_text' => ['label' => 'Header text', 'default' => '#ffffff'],
        'subbar_bg' => ['label' => 'Issue bar background', 'default' => '#2a2a30'],
        'subbar_text' => ['label' => 'Issue bar text', 'default' => '#ffffff'],
        'accent' => ['label' => 'Accent', 'default' => '#cc0a1e'],
        'accent_text' => ['label' => 'Accent text', 'default' => '#ffffff'],
        'event_accent' => ['label' => 'Event calendar', 'default' => '#cc0a1e'],
        'body_bg' => ['label' => 'Article background', 'default' => '#ffffff'],
        'body_text' => ['label' => 'Article text', 'default' => '#374151'],
        'footer_bg' => ['label' => 'Footer background', 'default' => '#16151a'],
        'footer_text' => ['label' => 'Footer text', 'default' => '#cbd5e1'],
        'page_bg' => ['label' => 'Page background', 'default' => '#f6f4ee'],
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'logo_path',
        'website_url',
        'social_links',
        'from_name',
        'from_email',
        'reply_to_email',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'owner_id',
        'settings',
        'structure',
        'palette',
    ];

    protected $casts = [
        'settings' => 'array',
        'social_links' => 'array',
        'structure' => 'array',
        'palette' => 'array',
        'smtp_username' => 'encrypted',
        'smtp_password' => 'encrypted',
    ];

    /**
     * Whether this publication has its own SMTP credentials configured.
     */
    public function hasSmtpConfigured(): bool
    {
        return filled($this->smtp_host) && filled($this->smtp_port);
    }

    /**
     * Whether any SMTP field is filled but host/port are not both present. In this
     * state the publication silently falls back to the platform mailer (auth alone
     * is not enough to route mail), so the UI should warn.
     */
    public function hasPartialSmtp(): bool
    {
        $anyFilled = collect([$this->smtp_host, $this->smtp_port, $this->smtp_username, $this->smtp_password])
            ->contains(fn ($value) => filled($value));

        return $anyFilled && ! $this->hasSmtpConfigured();
    }

    /**
     * Register (if needed) and return the mailer name to send this
     * publication's email through — its own SMTP, or the app default.
     */
    public function configuredMailerName(): string
    {
        if (! $this->hasSmtpConfigured()) {
            return config('mail.default');
        }

        $name = 'publication_'.$this->id;

        config(['mail.mailers.'.$name => [
            'transport' => 'smtp',
            'host' => $this->smtp_host,
            'port' => (int) $this->smtp_port,
            'username' => $this->smtp_username,
            'password' => $this->smtp_password,
            'encryption' => $this->smtp_encryption,
            'timeout' => null,
        ]]);

        return $name;
    }

    /**
     * Disk used for this publication's public media (logo, photos).
     */
    public static function mediaDisk(): string
    {
        return config('filesystems.media_disk', 'public');
    }

    /**
     * Public URL to the logo, or null when none is set.
     */
    public function logoUrl(): ?string
    {
        return $this->logo_path
            ? Storage::disk(static::mediaDisk())->url($this->logo_path)
            : null;
    }

    /**
     * The header/content/footer order for this publication's issues.
     * Always returns every section exactly once: the saved order first
     * (ignoring anything unrecognised), with any missing sections appended
     * in canonical order. Static across all of the publication's issues.
     *
     * @return list<string>
     */
    public function structureOrder(): array
    {
        $saved = is_array($this->structure)
            ? array_values(array_intersect($this->structure, self::STRUCTURE_SECTIONS))
            : [];

        foreach (self::STRUCTURE_SECTIONS as $section) {
            if (! in_array($section, $saved, true)) {
                $saved[] = $section;
            }
        }

        return $saved;
    }

    /**
     * The resolved palette: every configurable colour, falling back to its
     * default when unset or not a valid 6-digit hex value (which also keeps
     * the values safe to inline into email/HTML styles).
     *
     * @return array<string, string>
     */
    public function paletteColors(): array
    {
        $saved = is_array($this->palette) ? $this->palette : [];
        $colors = [];

        foreach (self::PALETTE_FIELDS as $key => $config) {
            $value = $saved[$key] ?? null;
            $colors[$key] = (is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value))
                ? $value
                : $config['default'];
        }

        // The event calendar colour follows the main accent unless it has been
        // explicitly customised, so existing publications are unaffected.
        if (! isset($saved['event_accent']) || ! preg_match('/^#[0-9a-fA-F]{6}$/', (string) ($saved['event_accent'] ?? ''))) {
            $colors['event_accent'] = $colors['accent'];
        }

        return $colors;
    }

    /**
     * Auto-assign a unique slug from the name when one isn't provided.
     */
    protected static function booted(): void
    {
        static::creating(function (Publication $publication) {
            if (blank($publication->slug)) {
                $publication->slug = static::uniqueSlug($publication->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'publication';
        $slug = $base;
        $suffix = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(Subscriber::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    /**
     * Team members (app users) with a role on this publication.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'publication_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function editors(): BelongsToMany
    {
        return $this->members()->wherePivot('role', 'editor');
    }
}
