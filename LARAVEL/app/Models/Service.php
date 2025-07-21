<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    // Usar la tabla 'services' creada por la migración
    protected $table = 'services';

    protected $fillable = [
        'nombre',
        'descripcion',
        'precio',
        'duracion_horas',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
            'precio' => 'decimal:2',
        ];
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'servicio_id');
    }
}
