<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'nombre' => 'Boda Completa',
                'descripcion' => 'Servicio completo de organización de bodas incluyendo decoración, catering, música y fotografía',
                'precio' => 2500.00,
                'duracion_horas' => 8,
                'activo' => true,
            ],
            [
                'nombre' => 'Fiesta de Cumpleaños',
                'descripcion' => 'Organización de fiestas de cumpleaños con decoración temática, entretenimiento y catering',
                'precio' => 800.00,
                'duracion_horas' => 4,
                'activo' => true,
            ],
            [
                'nombre' => 'Evento Corporativo',
                'descripcion' => 'Organización de eventos empresariales, conferencias y reuniones corporativas',
                'precio' => 1200.00,
                'duracion_horas' => 6,
                'activo' => true,
            ],
            [
                'nombre' => 'Quinceañera',
                'descripcion' => 'Celebración de quinceañeras con decoración elegante, música y servicios completos',
                'precio' => 1800.00,
                'duracion_horas' => 6,
                'activo' => true,
            ],
            [
                'nombre' => 'Baby Shower',
                'descripcion' => 'Celebración de baby shower con decoración temática y entretenimiento',
                'precio' => 600.00,
                'duracion_horas' => 3,
                'activo' => true,
            ],
            [
                'nombre' => 'Graduación',
                'descripcion' => 'Celebración de graduación con servicios de catering y entretenimiento',
                'precio' => 900.00,
                'duracion_horas' => 4,
                'activo' => true,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
