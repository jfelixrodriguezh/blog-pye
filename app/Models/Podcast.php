<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Podcast extends Model
{
    protected $fillable = ['title', 'slug', 'description', 'cover_image', 'status'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}
