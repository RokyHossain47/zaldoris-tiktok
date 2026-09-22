<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'installation_id',
        'push_token',
        'device_type',
        'os_version',
        'app_version',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
