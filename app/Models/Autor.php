<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autor extends Model
{
    use HasFactory;

    protected $table = 'autors';

    protected $fillable = ['name', 'description', 'photo'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }

    public function himnos()
    {
        return $this->hasMany(Himno::class);
    }
}
