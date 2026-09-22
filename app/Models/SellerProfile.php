<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SellerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'business_number',
        'id_proof_url',
        'sin_last4',
        'sin_verified_at',
        'status',
        'live_selling_active',
        'on_time_dispatch_rate',
        'total_orders',
        'total_dispatches',
        'audit_status',
        'packing_video_required',
    ];

    protected $casts = [
        'sin_verified_at' => 'datetime',
        'live_selling_active' => 'boolean',
        'packing_video_required' => 'boolean',
        'on_time_dispatch_rate' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
