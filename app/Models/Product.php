<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'title',
        'description',
        'price',
        'compare_price',
        'stock',
        'locked_stock',
        'images',
        'category',
        'sourcing_country',
        'supplier_proof_url',
        'dimensions',
        'is_natural_lighting_declared',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'images' => 'array',
        'is_natural_lighting_declared' => 'boolean',
        'stock' => 'integer',
        'locked_stock' => 'integer',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function streams(): BelongsToMany
    {
        return $this->belongsToMany(Stream::class, 'live_stream_products')
            ->withPivot(['is_pinned', 'session_stock_cap', 'session_units_sold'])
            ->withTimestamps();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getPrimaryImageAttribute(): string
    {
        if (!empty($this->images) && is_array($this->images) && count($this->images) > 0) {
            return $this->images[0];
        }
        return 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=60';
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->locked_stock);
    }
}
