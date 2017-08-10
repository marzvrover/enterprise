<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $fillable = ['name', 'description', 'location', 'slug', 'start', 'end', 'open_at'];

    public static function findBySlug($slug)
    {
        return self::where('slug', $slug)->firstOrFail();
    }
}
