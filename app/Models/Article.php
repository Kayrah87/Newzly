<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'issue_id',
        'title',
        'content',
        'author_id',
        'order',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(NewsletterIssue::class, 'issue_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
