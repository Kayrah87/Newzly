<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Issue extends Model
{
    use HasFactory;

    protected $fillable = [
        'publication_id',
        'title',
        'issue_number',
        'coverage_label',
        'release_date',
        'content',
        'status',
        'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'release_date' => 'date',
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class, 'issue_id')->orderBy('order');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(IssueDelivery::class);
    }

    public function isSent(): bool
    {
        return $this->status === 'sent';
    }

    public function markSent(): void
    {
        $this->forceFill([
            'status' => 'sent',
            'published_at' => $this->published_at ?? now(),
        ])->save();
    }
}
