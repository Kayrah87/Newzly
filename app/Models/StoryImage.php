<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StoryImage extends Model
{
    protected $fillable = [
        'story_id',
        'publication_id',
        'path',
        'caption',
        'order',
    ];

    public function story(): BelongsTo
    {
        return $this->belongsTo(Story::class);
    }

    /**
     * Absolute URL to the image (emails need absolute URLs).
     */
    public function url(): string
    {
        $url = Storage::disk(Publication::mediaDisk())->url($this->path);

        return Str::startsWith($url, ['http://', 'https://']) ? $url : url($url);
    }
}
