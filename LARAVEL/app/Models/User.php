<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    // Usar la tabla existente 'usuarios'
    protected $table = 'usuarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nombre',
        'email',
        'password',
        'telefono',
        'rol_id',
        'activo',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'activo' => 'boolean',
        ];
    }

    // Relaciones
    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }

    public function eventsAsClient()
    {
        return $this->hasMany(Event::class, 'cliente_id');
    }

    public function eventsAsEmployee()
    {
        return $this->hasMany(Event::class, 'empleado_id');
    }

    public function seguimientos()
    {
        return $this->hasMany(Seguimiento::class, 'usuario_id');
    }
}
