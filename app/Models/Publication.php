<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Publication extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'owner_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

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
