<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderReturn extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'order_item_id',
        'user_id',
        'reason_code',
        'description',
        'evidence_asset_ids',
        'refund_amount',
        'status',
        'instructions',
        'return_tracking_number',
        'return_deadline',
    ];

    protected $casts = [
        'evidence_asset_ids' => 'array',
        'refund_amount' => 'decimal:2',
        'return_deadline' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
