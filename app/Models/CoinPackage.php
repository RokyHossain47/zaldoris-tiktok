<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CoinPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'coins',
        'price_usd',
        'bonus_coins',
        'badge_tier',
        'is_popular',
        'is_active',
    ];

    protected $casts = [
        'coins' => 'integer',
        'bonus_coins' => 'integer',
        'price_usd' => 'decimal:2',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function getTotalCoinsAttribute(): int
    {
        return $this->coins + $this->bonus_coins;
    }
}
