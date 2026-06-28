<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

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

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('order');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(IssueDelivery::class);
    }

    /**
     * The issue's content stream — stories and blocks merged and sorted by
     * their shared `order`, so blocks render in between the articles.
     *
     * @return Collection<int, Story|Block>
     */
    public function orderedContent(bool $approvedStoriesOnly = false): Collection
    {
        $stories = $this->stories()->with('images', 'author');

        if ($approvedStoriesOnly) {
            $stories->approved();
        }

        return $stories->get()
            ->concat($this->blocks()->with('events')->get())
            ->sortBy('order')
            ->values();
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
