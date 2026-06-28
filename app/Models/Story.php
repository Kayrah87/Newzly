<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Story extends Model
{
    use HasFactory;

    public const LAYOUTS = ['standard', 'standard_clear', 'picture', 'picture_clear', 'title_only'];

    /**
     * Human labels for each layout, for the story form select.
     * The "clear" variants drop the filled accent banner: the header sits on the
     * article background and the title itself is rendered in the accent colour.
     *
     * @var array<string, string>
     */
    public const LAYOUT_LABELS = [
        'standard' => 'Standard — accent banner',
        'standard_clear' => 'Standard — clear header, accent title',
        'picture' => 'Picture — accent banner + photo',
        'picture_clear' => 'Picture — clear header, accent title + photo',
        'title_only' => 'Title only',
    ];

    public const SOURCE_ADMIN = 'admin';

    public const SOURCE_PUBLIC = 'public';

    public const STATUS_PENDING = 'pending';

    public const STATUS_APPROVED = 'approved';

    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'publication_id',
        'issue_id',
        'title',
        'content',
        'layout',
        'source',
        'status',
        'author_id',
        'submitter_name',
        'submitter_email',
        'order',
    ];

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(StoryImage::class)->orderBy('order');
    }

    public function heroImage(): ?StoryImage
    {
        return $this->images->first();
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_APPROVED);
    }
}
