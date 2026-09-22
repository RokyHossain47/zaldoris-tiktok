<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'current_tier_id',
        'xp_points',
        'lifetime_xp',
        'streak_days',
    ];

    protected $casts = [
        'xp_points' => 'integer',
        'lifetime_xp' => 'integer',
        'streak_days' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tier(): BelongsTo
    {
        return $this->belongsTo(RewardTier::class, 'current_tier_id');
    }
}
