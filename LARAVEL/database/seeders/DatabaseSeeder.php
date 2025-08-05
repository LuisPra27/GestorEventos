<?php

na    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            ServiceSeeder::class,
            UserSeeder::class,
        ]);
    }e Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);
    }
}
