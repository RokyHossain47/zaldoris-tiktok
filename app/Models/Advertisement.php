<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advertisement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'subtitle',
        'ad_type',
        'placement',
        'media_url',
        'link_url',
        'button_text',
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
