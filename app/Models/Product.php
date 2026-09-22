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
        'category_id',
        'title',
        'brand',
        'description',
        'price',
        'compare_price',
        'stock',
        'locked_stock',
        'images',
        'colors',
        'sizes',
        'specifications',
        'category',
        'sourcing_country',
        'supplier_proof_url',
        'dimensions',
        'is_natural_lighting_declared',
        'status',
        'is_featured',
        'is_trending',
        'is_live_product',
        'stream_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'images' => 'array',
        'colors' => 'array',
        'sizes' => 'array',
        'specifications' => 'array',
        'is_natural_lighting_declared' => 'boolean',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_live_product' => 'boolean',
        'stock' => 'integer',
        'locked_stock' => 'integer',
    ];

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class, 'stream_id');
    }

    public function scopeNormal($query)
    {
        return $query->where('is_live_product', false);
    }

    public function scopeLive($query)
    {
        return $query->where('is_live_product', true);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function categoryRel(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getCategoryNameAttribute(): string
    {
        if ($this->relationLoaded('category') && $this->getRelation('category') instanceof Category) {
            return $this->getRelation('category')->name;
        }
        if ($this->category_id) {
            $cat = Category::find($this->category_id);
            if ($cat) return $cat->name;
        }
        $raw = $this->attributes['category'] ?? null;
        return is_string($raw) && !empty($raw) ? ucfirst($raw) : 'General';
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

    public function getColorsListAttribute(): array
    {
        if (!empty($this->colors) && is_array($this->colors)) {
            $list = [];
            foreach ($this->colors as $c) {
                if (is_array($c) && !empty($c['name'])) {
                    $list[] = [
                        'name' => $c['name'],
                        'code' => !empty($c['code']) ? $c['code'] : '#1E293B',
                    ];
                } elseif (is_string($c) && trim($c) !== '') {
                    $list[] = [
                        'name' => trim($c),
                        'code' => '#1E293B',
                    ];
                }
            }
            if (count($list) > 0) return $list;
        }

        // Standard default colors fallback
        return [
            ['name' => 'Midnight Stealth', 'code' => '#1E293B'],
            ['name' => 'Teal Titanium', 'code' => '#0D2626'],
            ['name' => 'Silver Steel', 'code' => '#E2E8F0'],
            ['name' => 'Deep Violet', 'code' => '#7033FF'],
        ];
    }

    public function getSizesListAttribute(): array
    {
        if (!empty($this->sizes) && is_array($this->sizes)) {
            $list = [];
            foreach ($this->sizes as $s) {
                if (is_array($s) && !empty($s['name'])) {
                    $list[] = [
                        'name' => $s['name'],
                        'price_modifier' => (float) ($s['price_modifier'] ?? 0),
                    ];
                } elseif (is_string($s) && trim($s) !== '') {
                    $list[] = [
                        'name' => trim($s),
                        'price_modifier' => 0.00,
                    ];
                }
            }
            if (count($list) > 0) return $list;
        }

        // Standard default sizes fallback
        return [
            ['name' => 'Standard', 'price_modifier' => 0.00],
            ['name' => 'Large (+C$30)', 'price_modifier' => 30.00],
            ['name' => 'Pro Ultra (+C$70)', 'price_modifier' => 70.00],
        ];
    }

    public function getSpecsListAttribute(): array
    {
        $specs = is_array($this->specifications) ? $this->specifications : [];
        if (!isset($specs['Dimensions']) && $this->dimensions) {
            $specs['Dimensions'] = $this->dimensions;
        }
        if (!isset($specs['Sourcing Country']) && $this->sourcing_country) {
            $specs['Sourcing Country'] = $this->sourcing_country;
        }
        if (!isset($specs['Brand']) && $this->brand) {
            $specs['Brand'] = $this->brand;
        }
        return $specs;
    }

    public function getAverageRatingAttribute(): float
    {
        $avg = $this->reviews()->avg('rating');
        return $avg ? round($avg, 1) : 4.9;
    }

    public function getReviewsCountAttribute(): int
    {
        $cnt = $this->reviews()->count();
        return $cnt > 0 ? $cnt : 1280;
    }
}
