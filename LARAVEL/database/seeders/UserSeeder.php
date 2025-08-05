<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'nombre' => 'Administrador',
                'email' => 'admin@gestor.com',
                'password' => Hash::make('admin123'),
                'telefono' => '555-0001',
                'rol_id' => 3, // gerente
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Empleado Gestor',
                'email' => 'empleado@gestor.com',
                'password' => Hash::make('empleado123'),
                'telefono' => '555-0002',
                'rol_id' => 2, // empleado
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
