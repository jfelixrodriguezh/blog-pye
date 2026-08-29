<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'aplica_posts', 'aplica_podcasts','aplica_himnos'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function episodes()
    {
        return $this->belongsToMany(Episode::class, 'episode_categories');
    }

    public function scopeParaPosts($query)
    {
        return $query->where('aplica_posts', true);
    }

    public function scopeParaPodcasts($query)
    {
        return $query->where('aplica_podcasts', true);
    }

    public function scopeParaHimnos($query)
    {
        return $query->where('aplica_himnos', true);
    }

    // public function posts()
    // {
    //     return $this->belongsToMany(Post::class, 'post_categories');
    // }
}
