<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'director',
        'writers',
        'stars',
        'poster',
        'release_date',
        'duration',
        'url_720',
        'url_1080',
        'url_4k',
    ];

    public function categories(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}