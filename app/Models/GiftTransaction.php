<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GiftTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'stream_id',
        'sender_id',
        'receiver_id',
        'gift_id',
        'coin_amount',
        'creator_earning_usd',
        'platform_commission_usd',
    ];

    protected $casts = [
        'coin_amount' => 'integer',
        'creator_earning_usd' => 'decimal:2',
        'platform_commission_usd' => 'decimal:2',
    ];

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function gift(): BelongsTo
    {
        return $this->belongsTo(Gift::class);
    }
}
