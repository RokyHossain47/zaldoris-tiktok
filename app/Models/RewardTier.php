<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RewardTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'tier_level',
        'name',
        'min_xp',
        'max_xp',
        'benefits',
        'icon_url',
        'badge_color',
    ];

    protected $casts = [
        'tier_level' => 'integer',
        'min_xp' => 'integer',
        'max_xp' => 'integer',
        'benefits' => 'array',
    ];

    public function userRewards(): HasMany
    {
        return $this->hasMany(UserReward::class, 'current_tier_id');
    }
}
