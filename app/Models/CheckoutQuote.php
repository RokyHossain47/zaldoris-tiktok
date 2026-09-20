<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckoutQuote extends Model
{
    use HasFactory;

    protected $fillable = [
        'quote_id',
        'user_id',
        'currency',
        'items',
        'seller_groups',
        'shipping_address',
        'shipping_method',
        'promo_code',
        'item_subtotal',
        'discount_amount',
        'platform_fee',
        'processing_fee',
        'shipping_fee',
        'tax_amount',
        'total_amount',
        'status',
        'expires_at',
    ];

    protected $casts = [
        'items' => 'array',
        'seller_groups' => 'array',
        'shipping_address' => 'array',
        'item_subtotal' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'processing_fee' => 'decimal:2',
        'shipping_fee' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
