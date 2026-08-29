<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estrofa extends Model
{
    // Contenido fijo por himno, igual que hicimos con Versiculo.
    public $timestamps = false;

    protected $fillable = ['himno_id', 'tipo', 'numero', 'texto', 'orden'];

    public function himno()
    {
        return $this->belongsTo(Himno::class);
    }
}
