<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    protected $fillable = [
        'user_id',
        'device_name',
        'device_id',
        'device_type',
        'platform',
        'platform_version',
        'browser',
        'browser_version',
        'last_active',
        'is_active'
    ];

    protected $casts = [
        'last_active' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}