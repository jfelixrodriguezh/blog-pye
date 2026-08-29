<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tono extends Model
{
    protected $fillable = ['nombre'];

    public function himnos()
    {
        return $this->hasMany(Himno::class);
    }
}
