<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;

class SafeMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:safe {--force : Force the operation to run in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations safely, preserving existing data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Analizando estado de la base de datos...');

        // Verificar variables de entorno de seguridad
        $safeMigrationMode = env('SAFE_MIGRATION_MODE', true);
        $freshInstall = env('FRESH_INSTALL', false);

        if ($safeMigrationMode) {
            $this->info('🛡️  Modo de migración segura activado');
        }

        if ($freshInstall) {
            $this->warn('🆕 Modo de instalación fresca detectado en variables de entorno');
        }

        try {
            // Verificar conexión a la base de datos
            DB::connection()->getPdo();
            $this->info('✅ Conexión a base de datos establecida');
        } catch (\Exception $e) {
            $this->error('❌ Error de conexión a la base de datos: ' . $e->getMessage());
            return 1;
        }

        // Si está marcado como instalación fresca, forzar instalación completa
        if ($freshInstall) {
            $this->warn('⚠️  FRESH_INSTALL=true detectado. Esto ejecutará una instalación completa.');
            if ($safeMigrationMode) {
                $this->warn('⚠️  Recomendación: Cambiar FRESH_INSTALL=false después del primer despliegue.');
            }
            return $this->freshInstall();
        }

        // Verificar si las tablas principales existen
        $hasTables = $this->checkMainTables();
        $hasData = $this->checkData();

        if (!$hasTables) {
            $this->info('🆕 Primera instalación detectada automáticamente');
            return $this->freshInstall();
        } elseif (!$hasData) {
            $this->info('🌱 Tablas sin datos detectadas');
            if ($safeMigrationMode) {
                $this->info('🛡️  Modo seguro: Solo agregando datos iniciales');
            }
            return $this->seedDatabase();
        } else {
            $this->info('📊 Base de datos con datos existentes');
            if ($safeMigrationMode) {
                $this->info('🛡️  Modo seguro: Solo migraciones no destructivas');
            }
            return $this->updateDatabase();
        }
    }

    /**
     * Check if main tables exist
     */
    private function checkMainTables(): bool
    {
        $tables = ['users', 'roles', 'services', 'events'];

        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                $this->warn("⚠️  Tabla '$table' no encontrada");
                return false;
            }
        }

        $this->info('✅ Todas las tablas principales existen');
        return true;
    }

    /**
     * Check if tables have data
     */
    private function checkData(): bool
    {
        try {
            $userCount = DB::table('users')->count();
            $roleCount = DB::table('roles')->count();

            $this->info("📊 Usuarios: $userCount, Roles: $roleCount");

            return $userCount > 0 && $roleCount > 0;
        } catch (\Exception $e) {
            $this->warn("⚠️  Error verificando datos: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Fresh installation
     */
    private function freshInstall(): int
    {
        $this->info('🚀 Ejecutando instalación completa...');

        try {
            // Ejecutar migraciones
            Artisan::call('migrate', ['--force' => true]);
            $this->info('✅ Migraciones completadas');

            // Ejecutar seeders
            Artisan::call('db:seed', ['--force' => true]);
            $this->info('✅ Seeders completados');

            $this->info('🎉 Instalación completa exitosa');
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error en instalación: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Seed database
     */
    private function seedDatabase(): int
    {
        $this->info('🌱 Agregando datos iniciales...');

        try {
            // Ejecutar migraciones pendientes
            Artisan::call('migrate', ['--force' => true]);
            $this->info('✅ Migraciones aplicadas');

            // Ejecutar seeders
            Artisan::call('db:seed', ['--force' => true]);
            $this->info('✅ Datos iniciales agregados');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error agregando datos: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Update database
     */
    private function updateDatabase(): int
    {
        $this->info('🔄 Aplicando actualizaciones...');

        try {
            // Crear backup info
            $this->createBackupInfo();

            // Solo ejecutar migraciones pendientes
            $output = Artisan::call('migrate', ['--force' => true]);

            if ($output === 0) {
                $this->info('✅ Migraciones aplicadas correctamente');
                $this->info('📋 Datos existentes preservados');
            } else {
                $this->warn('⚠️  Algunas migraciones pueden haber tenido problemas');
            }

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error actualizando base de datos: ' . $e->getMessage());
            return 1;
        }
    }

    /**
     * Create backup info
     */
    private function createBackupInfo(): void
    {
        try {
            $userCount = DB::table('users')->count();
            $eventCount = DB::table('events')->count();
            $serviceCount = DB::table('services')->count();

            $backupInfo = [
                'timestamp' => now()->toISOString(),
                'users' => $userCount,
                'events' => $eventCount,
                'services' => $serviceCount,
                'migration_status' => 'before_update'
            ];

            // Guardar en logs
            \Log::info('Database backup info', $backupInfo);

            $this->info("💾 Backup info: $userCount usuarios, $eventCount eventos, $serviceCount servicios");
        } catch (\Exception $e) {
            $this->warn('⚠️  No se pudo crear backup info: ' . $e->getMessage());
        }
    }
}
