<?php

namespace App\Models;

use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    /** @use HasFactory<EventFactory> */
    use HasFactory;

    protected $fillable = [
        'block_id',
        'name',
        'date',
        'location',
        'description',
        'order',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }
}
