<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Versiculo extends Model
{
    // 31,101 filas de texto fijo no necesitan created_at/updated_at por fila.
    public $timestamps = false;

    protected $fillable = ['libro_id', 'capitulo', 'numero', 'texto'];

    public function libro()
    {
        return $this->belongsTo(Libro::class);
    }
}
