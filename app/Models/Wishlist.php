<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wishlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'preferred_variant',
        'preferred_quantity',
    ];

    protected $casts = [
        'preferred_variant' => 'array',
        'preferred_quantity' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'target_id');
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class, 'target_id');
    }
}
