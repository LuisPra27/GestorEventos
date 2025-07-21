<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // Usar la tabla existente 'eventos'
    protected $table = 'eventos';

    protected $fillable = [
        'titulo',
        'descripcion',
        'cliente_id',
        'servicio_id',
        'empleado_id',
        'fecha_evento',
        'ubicacion',
        'numero_invitados',
        'presupuesto',
        'requisitos_especiales',
        'estado',
    ];

    protected function casts(): array
    {
        return [
            'fecha_evento' => 'datetime',
            'presupuesto' => 'decimal:2',
        ];
    }

    // Relaciones
    public function cliente()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function servicio()
    {
        return $this->belongsTo(Service::class, 'servicio_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'evento_id');
    }
}
