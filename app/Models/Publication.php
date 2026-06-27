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
    ];

    protected $casts = [
        'settings' => 'array',
        'social_links' => 'array',
        'smtp_username' => 'encrypted',
        'smtp_password' => 'encrypted',
    ];

    /**
     * Whether this publication has its own SMTP credentials configured.
     */
    public function hasSmtpConfigured(): bool
    {
        return filled($this->smtp_host)
            && filled($this->smtp_port)
            && filled($this->smtp_username)
            && filled($this->smtp_password);
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
