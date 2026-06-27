<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Newsletter extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function issues(): HasMany
    {
        return $this->hasMany(NewsletterIssue::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'newsletter_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function editors()
    {
        return $this->users()->wherePivot('role', 'editor');
    }

    public function recipients()
    {
        return $this->users()->wherePivot('role', 'recipient');
    }
}
