<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'phone_verified_at',
        'email_verified_at',
        'password',
        'avatar',
        'role',
        'is_vip',
        'vip_badge_tier',
        'coin_balance',
        'earnings_usd',
        'is_verified',
        'fast_shipper_badge',
        'chargeback_count',
        'return_strike_count',
        'shipping_strike_count',
        'overselling_strike_count',
        'is_banned_from_auctions',
        'is_suspended',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_vip' => 'boolean',
            'is_verified' => 'boolean',
            'fast_shipper_badge' => 'boolean',
            'is_banned_from_auctions' => 'boolean',
            'is_suspended' => 'boolean',
            'coin_balance' => 'integer',
            'earnings_usd' => 'decimal:2',
        ];
    }

    public function sellerProfile(): HasOne
    {
        return $this->hasOne(SellerProfile::class);
    }

    public function creatorProfile(): HasOne
    {
        return $this->hasOne(CreatorProfile::class);
    }

    public function streams(): HasMany
    {
        return $this->hasMany(Stream::class, 'host_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'seller_id');
    }

    public function auctions(): HasMany
    {
        return $this->hasMany(Auction::class, 'seller_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    public function ordersAsBuyer(): HasMany
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }

    public function ordersAsSeller(): HasMany
    {
        return $this->hasMany(Order::class, 'seller_id');
    }

    public function coinTransactions(): HasMany
    {
        return $this->hasMany(CoinTransaction::class);
    }

    public function sentGifts(): HasMany
    {
        return $this->hasMany(GiftTransaction::class, 'sender_id');
    }

    public function receivedGifts(): HasMany
    {
        return $this->hasMany(GiftTransaction::class, 'receiver_id');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')->withTimestamps();
    }

    public function following(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')->withTimestamps();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return in_array($this->role, ['admin', 'moderator', 'dispute_manager']);
    }

    public function isSeller(): bool
    {
        return in_array($this->role, ['seller', 'admin']);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class);
    }

    public function defaultAddress(): HasOne
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }

    public function isCreator(): bool
    {
        return in_array($this->role, ['creator', 'admin']);
    }

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar ? asset($this->avatar) : 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=FE2C55&color=fff';
    }
}
