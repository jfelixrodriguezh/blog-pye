<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Himno extends Model
{
    protected $fillable = [
        'himnario_id', 'autor_id', 'category_id', 'tono_id',
        'titulo', 'numero', 'referencia', 'informacion', 'es_local', 'audio_url',
        'youtube_url', 'partitura_es_local','partitura', 'status', 'published_at',
    ];

    protected $casts = [
        'es_local' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function himnario()
    {
        return $this->belongsTo(Himnario::class);
    }

    public function autor()
    {
        return $this->belongsTo(Autor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function tono()
    {
        return $this->belongsTo(Tono::class);
    }

    public function estrofas()
    {
        return $this->hasMany(Estrofa::class)->orderBy('orden');
    }

    public function getYoutubeEmbedUrlAttribute(): ?string
    {
        if (! $this->youtube_url) {
            return null;
        }

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/))([a-zA-Z0-9_-]{11})/', $this->youtube_url, $matches)) {
            return 'https://www.youtube.com/embed/'.$matches[1];
        }

        return null;
    }
}
