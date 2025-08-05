<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'nombre' => 'cliente',
                'descripcion' => 'Usuario cliente que solicita eventos',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'empleado',
                'descripcion' => 'Empleado que gestiona y ejecuta eventos',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'gerente',
                'descripcion' => 'Gerente con acceso completo al sistema',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
