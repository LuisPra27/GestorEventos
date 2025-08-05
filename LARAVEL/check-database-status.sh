#!/bin/bash

# Archivo: check-database-status.sh
# Propósito: Verificar el estado de la base de datos antes de ejecutar migraciones

# Función para verificar si una tabla existe
table_exists() {
    local table_name=$1
    php artisan tinker --execute="echo Schema::hasTable('$table_name') ? 'true' : 'false';" 2>/dev/null | grep -q "true"
}

# Función para verificar si hay datos en una tabla
table_has_data() {
    local table_name=$1
    local count=$(php artisan tinker --execute="echo DB::table('$table_name')->count();" 2>/dev/null | grep -E '^[0-9]+$')
    [ "$count" -gt 0 ] 2>/dev/null
}

# Función principal
check_database_status() {
    echo "🔍 Verificando estado de la base de datos..."
    
    # Verificar conexión
    if ! php artisan tinker --execute="DB::connection()->getPdo();" 2>/dev/null; then
        echo "❌ No se puede conectar a la base de datos"
        return 1
    fi
    
    echo "✅ Conexión a base de datos establecida"
    
    # Verificar si las tablas principales existen
    if table_exists "users" && table_exists "roles" && table_exists "services"; then
        echo "📊 Tablas principales detectadas"
        
        # Verificar si hay datos importantes
        if table_has_data "users" && table_has_data "roles"; then
            echo "📋 Datos existentes detectados en la base de datos"
            echo "MODE=update"  # Modo actualización (solo migraciones pendientes)
        else
            echo "📭 Tablas existen pero sin datos iniciales"
            echo "MODE=seed"    # Modo seed (migraciones + seeders)
        fi
    else
        echo "🆕 Base de datos nueva detectada"
        echo "MODE=fresh"      # Modo fresco (migraciones completas + seeders)
    fi
}

# Ejecutar verificación
check_database_status
