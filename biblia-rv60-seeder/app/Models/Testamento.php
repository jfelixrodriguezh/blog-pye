<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testamento extends Model
{
    protected $fillable = ['nombre', 'orden'];

    public function libros()
    {
        return $this->hasMany(Libro::class)->orderBy('orden');
    }
}
