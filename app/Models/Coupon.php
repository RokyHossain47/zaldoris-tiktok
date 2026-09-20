<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'per_user_limit',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'per_user_limit' => 'integer',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * Check if coupon is valid for a given user and subtotal.
     */
    public function validateFor(?User $user, float $subtotal): array
    {
        if (!$this->is_active) {
            return [
                'valid' => false,
                'message' => "Coupon '{$this->code}' is no longer active.",
                'discount' => 0.00
            ];
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return [
                'valid' => false,
                'message' => "Coupon '{$this->code}' is not yet available.",
                'discount' => 0.00
            ];
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return [
                'valid' => false,
                'message' => "Coupon '{$this->code}' has expired.",
                'discount' => 0.00
            ];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return [
                'valid' => false,
                'message' => "Coupon '{$this->code}' has reached its maximum global usage limit.",
                'discount' => 0.00
            ];
        }

        if ($subtotal < (float)$this->min_order_amount) {
            $minFormatted = number_format($this->min_order_amount, 2);
            return [
                'valid' => false,
                'message' => "Minimum order amount of \${$minFormatted} required to use coupon '{$this->code}'.",
                'discount' => 0.00
            ];
        }

        if ($user && $this->per_user_limit > 0) {
            $userUsageCount = $this->usages()->where('user_id', $user->id)->count();
            if ($userUsageCount >= $this->per_user_limit) {
                return [
                    'valid' => false,
                    'message' => "You have already used coupon '{$this->code}' the maximum allowable times.",
                    'discount' => 0.00
                ];
            }
        }

        $discount = $this->calculateDiscount($subtotal);

        return [
            'valid' => true,
            'message' => "Coupon '{$this->code}' applied successfully!",
            'discount' => $discount,
            'coupon' => $this
        ];
    }

    /**
     * Calculate discount based on subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.00;
        }

        $discount = 0.00;
        if ($this->type === 'percentage') {
            $discount = round(($subtotal * (float)$this->value) / 100, 2);
            if ($this->max_discount_amount !== null && $discount > (float)$this->max_discount_amount) {
                $discount = (float)$this->max_discount_amount;
            }
        } else {
            // Fixed discount amount
            $discount = min($subtotal, (float)$this->value);
        }

        return round(max(0, $discount), 2);
    }
}
