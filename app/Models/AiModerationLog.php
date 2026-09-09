<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiModerationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_type',
        'content_id',
        'user_id',
        'flagged_content',
        'violation_type',
        'confidence_score',
        'action_taken',
    ];

    protected $casts = [
        'confidence_score' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
