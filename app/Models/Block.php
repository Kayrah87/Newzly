<?php

namespace App\Models;

use Database\Factories\BlockFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Block extends Model
{
    /** @use HasFactory<BlockFactory> */
    use HasFactory;

    public const TYPE_EVENTS = 'events';

    /**
     * Available block types => human label (drives the "Add Block" menu).
     * Add new block types here as they are built.
     *
     * @var array<string, string>
     */
    public const TYPE_LABELS = [
        self::TYPE_EVENTS => 'Events Block',
    ];

    /** Available block types. */
    public const TYPES = [self::TYPE_EVENTS];

    /**
     * Title rendering styles => human label. accent = filled accent banner,
     * plain = clear header with an accent-coloured title, none = no title.
     */
    public const TITLE_STYLES = [
        'accent' => 'Accent banner',
        'plain' => 'Clear header, accent title',
        'none' => 'No title',
    ];

    protected $fillable = [
        'issue_id',
        'publication_id',
        'type',
        'title',
        'title_style',
        'intro',
        'order',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }

    public function publication(): BelongsTo
    {
        return $this->belongsTo(Publication::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class)->orderBy('order');
    }
}
