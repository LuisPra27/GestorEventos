<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('services')->insert([
            [
                'nombre' => 'Boda Completa',
                'descripcion' => 'Organización completa de boda incluyendo decoración, catering y música',
                'precio' => 500.00,
                'duracion_horas' => 8,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Fiesta de Cumpleaños',
                'descripcion' => 'Organización de fiesta de cumpleaños con decoración temática',
                'precio' => 800.00,
                'duracion_horas' => 4,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Evento Corporativo',
                'descripcion' => 'Organización de eventos empresariales y conferencias',
                'precio' => 250.00,
                'duracion_horas' => 6,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Baby Shower',
                'descripcion' => 'Organización de baby shower con decoración y catering',
                'precio' => 600.00,
                'duracion_horas' => 3,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Graduación',
                'descripcion' => 'Celebración de graduación con ceremonia y recepción',
                'precio' => 120.00,
                'duracion_horas' => 5,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nombre' => 'Aniversario',
                'descripcion' => 'Celebración de aniversario de bodas',
                'precio' => 150.00,
                'duracion_horas' => 6,
                'activo' => true,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
