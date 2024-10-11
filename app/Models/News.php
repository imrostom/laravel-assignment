<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = ['title', 'content', 'image', 'url', 'published_at', 'source', 'platform'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function getPublishedAtAttribute($value): string
    {
        return Carbon::parse($value)->format('d M Y, H:i A');
    }
}
