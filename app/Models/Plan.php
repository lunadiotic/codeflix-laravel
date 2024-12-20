<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'title',
        'price',
        'duration',
        'resolution',
        'max_devices',
    ];
}