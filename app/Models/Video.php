<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;

    protected $fillable = ['titulo', 'url', 'status'];

    public function scopeActivos($query)
    {
        return $query->where('status', 'activo');
    }

    /**
     * Extrae el ID del video a partir de distintos formatos de URL de YouTube:
     * watch?v=, youtu.be/, embed/, shorts/.
     */
    public function getYoutubeIdAttribute(): ?string
    {
        $url = $this->url ?? '';

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/', $url, $m)) {
            return $m[1];
        }

        return null;
    }

    public function getEmbedUrlAttribute(): ?string
    {
        return $this->youtube_id
            ? 'https://www.youtube.com/embed/'.$this->youtube_id
            : null;
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->youtube_id
            ? 'https://img.youtube.com/vi/'.$this->youtube_id.'/hqdefault.jpg'
            : null;
    }
}
