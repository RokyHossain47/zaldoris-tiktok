<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuthChallenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'challenge_type',
        'destination',
        'code',
        'token',
        'payload',
        'expires_at',
        'resend_available_at',
        'verified_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'expires_at' => 'datetime',
        'resend_available_at' => 'datetime',
        'verified_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
