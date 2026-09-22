<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StreamMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'stream_id',
        'user_id',
        'username_display',
        'message',
        'is_bot',
        'message_type',
    ];

    protected $casts = [
        'is_bot' => 'boolean',
    ];

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
