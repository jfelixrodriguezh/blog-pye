<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'podcast_id', 'title', 'slug', 'summary', 'show_notes', 'autor_id',
        'es_local', 'audio_url', 'duration', 'season_number', 'episode_number',
        'status', 'published_at',
    ];

    protected $casts = [
        'es_local' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function podcast()
    {
        return $this->belongsTo(Podcast::class);
    }

    public function autor()
    {
        return $this->belongsTo(Autor::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'episode_categories');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'episode_tags');
    }

    public function getIsSpotifyEmbedAttribute(): bool
    {
        return ! $this->es_local
            && str_contains($this->audio_url, 'spotify.com')
            && str_contains($this->audio_url, '/embed');
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->show_notes ?? ''));

        return max(1, (int) ceil($words / 200));
    }
}
