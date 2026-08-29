<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Himnario extends Model
{
    protected $fillable = ['nombre', 'slug'];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function himnos()
    {
        return $this->hasMany(Himno::class);
    }
}
