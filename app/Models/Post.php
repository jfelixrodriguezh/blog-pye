<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'autor_id', 'title', 'category_id', 'slug', 'content', 'summary',
        'status', 'post_image', 'featured', 'meta_title', 'meta_description', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function autor()
    {
        return $this->belongsTo(Autor::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getReadingTimeAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content));

        return max(1, (int) ceil($words / 200));
    }

    // public function categories()
    // {
    //     return $this->belongsToMany(Category::class, 'post_categories');
    // }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'post_tags');
    }

    public function markAsFeatured(): void
    {
        static::where('id', '!=', $this->id)->update(['featured' => false]);
        $this->update(['featured' => true]);
    }
}
