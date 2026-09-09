<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CoinTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'amount_coins',
        'amount_usd',
        'hst_tax_usd',
        'payment_method',
        'reference_id',
        'description',
    ];

    protected $casts = [
        'amount_coins' => 'integer',
        'amount_usd' => 'decimal:2',
        'hst_tax_usd' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
