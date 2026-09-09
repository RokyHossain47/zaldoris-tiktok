<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gift extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'coin_cost',
        'icon_url',
        'animation_type',
        'tier_ladder',
        'is_active',
    ];

    protected $casts = [
        'coin_cost' => 'integer',
        'is_active' => 'boolean',
    ];

    public function giftTransactions(): HasMany
    {
        return $this->hasMany(GiftTransaction::class);
    }
}
