<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // Usar la tabla 'events' creada por la migración
    protected $table = 'events';

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
        'notas_especiales',
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
    public function user()
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'servicio_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'evento_id');
    }
}
