<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Stream extends Model
{
    use HasFactory;

    protected $fillable = [
        'host_id',
        'title',
        'description',
        'category',
        'stream_type',
        'agora_channel',
        'agora_token',
        'stream_url',
        'thumbnail_url',
        'is_live',
        'viewer_count',
        'total_likes',
        'total_sales_amount',
        'total_gift_coins',
        'started_at',
        'ended_at',
        'is_boosted',
        'failover_count',
    ];

    protected $casts = [
        'is_live' => 'boolean',
        'is_boosted' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'total_sales_amount' => 'decimal:2',
    ];

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(StreamMessage::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'live_stream_products')
            ->withPivot(['is_pinned', 'session_stock_cap', 'session_units_sold'])
            ->withTimestamps();
    }

    public function auctions(): HasMany
    {
        return $this->hasMany(Auction::class);
    }

    public function giftTransactions(): HasMany
    {
        return $this->hasMany(GiftTransaction::class);
    }
}
