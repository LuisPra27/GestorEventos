<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'nombre' => 'Administrador',
                'email' => 'admin@gestor.com',
                'password' => Hash::make('admin123'),
                'telefono' => '1234567890',
                'rol_id' => 3, // Gerente
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'Juan Empleado',
                'email' => 'empleado@gestor.com',
                'password' => Hash::make('empleado123'),
                'telefono' => '0987654321',
                'rol_id' => 2, // Empleado
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombre' => 'María Cliente',
                'email' => 'cliente@gestor.com',
                'password' => Hash::make('cliente123'),
                'telefono' => '5555555555',
                'rol_id' => 1, // Cliente
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
