<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    protected $fillable = [
        'name',
        'artist',
        'cover_image',
        'description',
    ];

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }
}
