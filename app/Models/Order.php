<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'buyer_id',
        'seller_id',
        'stream_id',
        'auction_id',
        'coupon_id',
        'coupon_code',
        'discount_amount',
        'subtotal',
        'hst_tax',
        'shipping_fee',
        'insurance_fee',
        'platform_commission',
        'total_amount',
        'payment_status',
        'payment_method',
        'customer_email',
        'customer_name',
        'shipping_address',
        'status',
        'dispatch_deadline',
        'shipped_at',
        'delivered_at',
        'tracking_number',
        'carrier',
        'packing_video_url',
        'requires_signature',
        'is_store_credit_refund',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'hst_tax' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'insurance_fee' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'requires_signature' => 'boolean',
        'is_store_credit_refund' => 'boolean',
        'dispatch_deadline' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function dispute(): HasOne
    {
        return $this->hasOne(Dispute::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function isWithinCancellationGracePeriod(): bool
    {
        // 24-hour grace period for un-dispatched items (SRS #6)
        return $this->status === 'pending' && $this->created_at->diffInHours(now()) <= 24;
    }
}
