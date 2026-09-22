<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'bio',
        'follower_count',
        'following_count',
        'likes_count',
        'subscription_fee',
        'revenue_share_rate',
    ];

    protected $casts = [
        'subscription_fee' => 'decimal:2',
        'revenue_share_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
