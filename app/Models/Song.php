<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'source_url',
        'duration',
        'album_id',
    ];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}

