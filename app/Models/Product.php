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

    public static function normalizeImageUrl(?string $img): string
    {
        if (empty($img)) {
            return 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80';
        }

        $img = trim($img);

        // If it starts with http:// or https://
        if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://')) {
            $parsed = parse_url($img);
            $path = $parsed['path'] ?? '';
            // If it's a local uploaded image, dynamically resolve with asset() for current domain/port/subpath
            if (str_contains($path, 'uploads/')) {
                $subPath = substr($path, strpos($path, 'uploads/'));
                return asset($subPath);
            }
            return $img;
        }

        // Relative path
        return asset(ltrim($img, '/'));
    }

    public function getGalleryImagesAttribute(): array
    {
        $raw = $this->images;
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode(',', $raw)));
        }
        $list = [];
        if (is_array($raw)) {
            foreach ($raw as $img) {
                if (is_string($img) && trim($img) !== '') {
                    $list[] = static::normalizeImageUrl(trim($img));
                }
            }
        }
        if (count($list) === 0) {
            $list[] = 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=800&auto=format&fit=crop&q=80';
        }
        return array_values($list);
    }

    public function getPrimaryImageAttribute(): string
    {
        $gallery = $this->gallery_images;
        return $gallery[0] ?? 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500&auto=format&fit=crop&q=60';
    }

    public function getAvailableStockAttribute(): int
    {
        return max(0, $this->stock - $this->locked_stock);
    }

    public function getColorsListAttribute(): array
    {
        $raw = $this->colors;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (!empty($raw) && is_array($raw)) {
            $list = [];
            foreach ($raw as $c) {
                if (is_array($c) && !empty($c['name'])) {
                    $list[] = [
                        'name' => trim($c['name']),
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
        $raw = $this->sizes;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        if (!empty($raw) && is_array($raw)) {
            $list = [];
            foreach ($raw as $s) {
                if (is_array($s) && !empty($s['name'])) {
                    $list[] = [
                        'name' => trim($s['name']),
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
        $raw = $this->specifications;
        if (is_string($raw)) {
            $raw = json_decode($raw, true);
        }
        $normalized = [];
        if (is_array($raw)) {
            foreach ($raw as $k => $v) {
                if (is_array($v)) {
                    $specKey = trim($v['key'] ?? $v['name'] ?? '');
                    $specVal = trim($v['val'] ?? $v['value'] ?? '');
                    if ($specKey !== '' && $specVal !== '') {
                        $normalized[$specKey] = $specVal;
                    } elseif (!empty($v)) {
                        $filtered = array_filter($v);
                        if (!empty($filtered)) {
                            $normalized[$k] = implode(', ', $filtered);
                        }
                    }
                } elseif (is_string($v) || is_numeric($v)) {
                    if (is_string($k) && !is_numeric($k)) {
                        if (trim((string) $v) !== '') {
                            $normalized[$k] = (string) $v;
                        }
                    } else {
                        if (str_contains((string) $v, ':')) {
                            [$sk, $sv] = explode(':', (string) $v, 2);
                            if (trim($sk) !== '' && trim($sv) !== '') {
                                $normalized[trim($sk)] = trim($sv);
                            }
                        } else {
                            if (trim((string) $v) !== '') {
                                $normalized['Specification ' . ($k + 1)] = (string) $v;
                            }
                        }
                    }
                }
            }
        }

        return $normalized;
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
