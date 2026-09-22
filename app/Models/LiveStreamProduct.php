<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveStreamProduct extends Pivot
{
    protected $table = 'live_stream_products';

    protected $fillable = [
        'stream_id',
        'product_id',
        'is_pinned',
        'session_stock_cap',
        'session_units_sold',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'session_stock_cap' => 'integer',
        'session_units_sold' => 'integer',
    ];

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
