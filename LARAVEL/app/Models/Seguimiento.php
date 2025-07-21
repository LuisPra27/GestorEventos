<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguimiento extends Model
{
    protected $fillable = [
        'evento_id',
        'usuario_id',
        'comentario',
        'tipo',
    ];

    // Relaciones
    public function evento()
    {
        return $this->belongsTo(Event::class, 'evento_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
