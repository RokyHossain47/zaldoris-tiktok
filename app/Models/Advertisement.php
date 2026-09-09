<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'ad_type',
        'media_url',
        'link_url',
        'interval_minutes',
        'impressions',
        'clicks',
        'is_active',
    ];

    protected $casts = [
        'interval_minutes' => 'integer',
        'impressions' => 'integer',
        'clicks' => 'integer',
        'is_active' => 'boolean',
    ];
}
