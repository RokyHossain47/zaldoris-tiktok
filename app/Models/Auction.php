<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'stream_id',
        'product_id',
        'title',
        'description',
        'image_url',
        'starting_bid',
        'reserve_price',
        'current_bid',
        'highest_bidder_id',
        'min_bid_step',
        'status',
        'fail_count',
        'is_blurred',
        'relist_available_at',
        'seller_approved_relist',
        'starts_at',
        'ends_at',
    ];

    protected $casts = [
        'starting_bid' => 'decimal:2',
        'reserve_price' => 'decimal:2',
        'current_bid' => 'decimal:2',
        'min_bid_step' => 'decimal:2',
        'fail_count' => 'integer',
        'is_blurred' => 'boolean',
        'seller_approved_relist' => 'boolean',
        'relist_available_at' => 'datetime',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function highestBidder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'highest_bidder_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class)->orderBy('amount', 'desc');
    }

    public function getNextMinimumBidAttribute(): float
    {
        return (float) ($this->current_bid + $this->min_bid_step);
    }

    public function isExpired(): bool
    {
        return $this->ends_at && now()->greaterThan($this->ends_at);
    }
}
