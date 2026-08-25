<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Libro extends Model
{
    protected $fillable = ['testamento_id', 'nombre', 'slug', 'abreviatura', 'orden'];

    // Con esto, cualquier {libro} en una ruta se busca por slug en vez de por id,
    // sin tener que escribir {libro:slug} cada vez.
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function testamento()
    {
        return $this->belongsTo(Testamento::class);
    }

    public function versiculos()
    {
        return $this->hasMany(Versiculo::class);
    }
}
