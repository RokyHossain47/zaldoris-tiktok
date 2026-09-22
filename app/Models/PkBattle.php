<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PkBattle extends Model
{
    use HasFactory;

    protected $fillable = [
        'stream1_id',
        'stream2_id',
        'host1_id',
        'host2_id',
        'host1_score',
        'host2_score',
        'duration_seconds',
        'starts_at',
        'ends_at',
        'status',
        'winner_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'host1_score' => 'integer',
        'host2_score' => 'integer',
    ];

    public function stream(): BelongsTo
    {
        return $this->belongsTo(Stream::class, 'stream1_id');
    }

    public function stream1(): BelongsTo
    {
        return $this->belongsTo(Stream::class, 'stream1_id');
    }

    public function stream2(): BelongsTo
    {
        return $this->belongsTo(Stream::class, 'stream2_id');
    }

    public function host1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host1_id');
    }

    public function host2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host2_id');
    }

    public function hostUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host1_id');
    }

    public function challengerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host2_id');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function winnerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }
}
