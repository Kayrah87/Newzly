<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_UNSUBSCRIBED = 'unsubscribed';

    protected $fillable = [
        'publication_id',
        'email',
        'name',
        'status',
        'consent_at',
        'consent_ip',
        'consent_source',
        'confirmation_token',
        'confirmed_at',
        'unsubscribe_token',
        'unsubscribed_at',
    ];

    protected $casts = [
        'consent_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Subscriber $subscriber) {
            $subscriber->unsubscribe_token ??= Str::random(40);

            if ($subscriber->status === self::STATUS_PENDING) {
                $subscriber->confirmation_token ??= Str::random(40);
            }
        });
    }

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function consentEvents(): HasMany
    {
        return $this->hasMany(ConsentEvent::class)->latest('created_at');
    }

    /** Confirmed and not unsubscribed — i.e. mailable. */
    public function scopeMailable(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_CONFIRMED);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function isMailable(): bool
    {
        return $this->status === self::STATUS_CONFIRMED;
    }

    /**
     * Append a consent/audit event for this subscriber.
     */
    public function recordEvent(string $event, array $meta = [], ?string $ip = null, ?string $userAgent = null): ConsentEvent
    {
        return $this->consentEvents()->create([
            'publication_id' => $this->publication_id,
            'event' => $event,
            'ip' => $ip,
            'user_agent' => $userAgent,
            'meta' => $meta ?: null,
        ]);
    }

    /**
     * Mark the subscriber confirmed (completes double opt-in).
     */
    public function confirm(?string $ip = null, ?string $userAgent = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_CONFIRMED,
            'confirmed_at' => $this->confirmed_at ?? now(),
            'consent_at' => $this->consent_at ?? now(),
            'consent_ip' => $this->consent_ip ?? $ip,
            'confirmation_token' => null,
        ])->save();

        $this->recordEvent('confirmed', [], $ip, $userAgent);
    }

    /**
     * Mark the subscriber unsubscribed.
     */
    public function unsubscribe(?string $ip = null, string $source = 'self', ?string $userAgent = null): void
    {
        $this->forceFill([
            'status' => self::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now(),
        ])->save();

        $this->recordEvent('unsubscribed', ['source' => $source], $ip, $userAgent);
    }
}
