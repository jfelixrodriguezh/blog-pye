<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'aplica_posts', 'aplica_podcasts'];

    public function posts()
    {
        return $this->belongsToMany(Post::class, 'post_tags');
    }

        public function episodes()
    {
        return $this->belongsToMany(Episode::class, 'episode_tags');
    }

    public function scopeParaPosts($query)
    {
        return $query->where('aplica_posts', true);
    }

    public function scopeParaPodcasts($query)
    {
        return $query->where('aplica_podcasts', true);
    }
}
