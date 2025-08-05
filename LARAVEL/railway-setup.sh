#!/bin/bash

# Railway Setup Scrip# Ejecutar migraciones solo si la base de datos está disponible
if [ ! -z "$PGHOST" ]; then
    echo "📦 Iniciando configuración de base de datos..."

    # Esperar a que la base de datos esté disponible
    echo "⏳ Esperando conexión a la base de datos..."
    timeout_counter=0
    until timeout 15 php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>/dev/null | grep -q "OK"; do
        echo "⏳ Base de datos no disponible, esperando 10 segundos... (intento $((++timeout_counter)))"
        if [ $timeout_counter -ge 6 ]; then  # 60 segundos total
            echo "❌ Timeout: No se pudo conectar a la base de datos después de 60 segundos"
            exit 1
        fi
        sleep 10
    done

    echo "✅ Conexión a base de datos establecida"

    # Usar nuestro comando de migración segura
    echo "🔧 Ejecutando migraciones seguras..."
    if php artisan migrate:safe --force; then
        echo "✅ Migraciones completadas exitosamente"
    else
        echo "⚠️  Problemas con migraciones, intentando método tradicional..."
        php artisan migrate --force || echo "❌ Error en migraciones tradicionales"
    fi
else
    echo "⚠️  Saltando migraciones - Base de datos no configurada"
fis
# Este script se ejecuta automáticamente en Railway para configurar el entorno

set -e

echo "🚀 Iniciando configuración para Railway..."

# Verificar variables de entorno de PostgreSQL
if [ -z "$PGHOST" ]; then
    echo "⚠️  ADVERTENCIA: Variables de PostgreSQL no encontradas."
    echo "   Asegúrate de conectar el servicio PostgreSQL en Railway."
else
    echo "✅ PostgreSQL detectado: $PGHOST:$PGPORT"
fi

# Configurar variables de entorno de Laravel
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}
export APP_KEY=${APP_KEY:-}

# Si no hay APP_KEY, generar una
if [ -z "$APP_KEY" ]; then
    echo "🔑 Generando clave de aplicación..."
    php artisan key:generate --force --show
fi

# Configurar URL de la aplicación si está disponible
if [ ! -z "$RAILWAY_PUBLIC_DOMAIN" ]; then
    export APP_URL="https://$RAILWAY_PUBLIC_DOMAIN"
    echo "🌐 URL configurada: $APP_URL"
fi

# Ejecutar migraciones solo si la base de datos está disponible
if [ ! -z "$PGHOST" ]; then
    echo "📦 Iniciando configuración de base de datos..."

    # Esperar a que la base de datos esté disponible
    echo "⏳ Esperando conexión a la base de datos..."
    timeout_counter=0
    until timeout 15 php artisan tinker --execute="DB::connection()->getPdo(); echo 'OK';" 2>/dev/null | grep -q "OK"; do
        echo "⏳ Base de datos no disponible, esperando 10 segundos... (intento $((++timeout_counter)))"
        if [ $timeout_counter -ge 6 ]; then  # 60 segundos total
            echo "❌ Timeout: No se pudo conectar a la base de datos después de 60 segundos"
            exit 1
        fi
        sleep 10
    done

    echo "✅ Conexión a base de datos establecida"

    # Usar nuestro comando de migración segura
    echo "🔧 Ejecutando migraciones seguras..."
    if php artisan migrate:safe --force; then
        echo "✅ Migraciones completadas exitosamente"
    else
        echo "⚠️  Problemas con migraciones, intentando método tradicional..."

        # Verificar si hay datos existentes antes de migrar
        echo "🔍 Verificando si hay datos existentes..."
        USER_COUNT=$(php artisan tinker --execute="echo DB::table('users')->count();" 2>/dev/null | grep -E '^[0-9]+$' | head -1)

        if [ "$USER_COUNT" -gt 0 ] 2>/dev/null; then
            echo "📊 Datos existentes detectados ($USER_COUNT usuarios), solo aplicando migraciones pendientes..."
            php artisan migrate --force
        else
            echo "🆕 Base de datos vacía, ejecutando instalación completa..."
            php artisan migrate --force
            php artisan db:seed --force
        fi
    fi
else
    echo "⚠️  Saltando migraciones - Base de datos no configurada"
fi

# Optimizar Laravel para producción
echo "⚡ Optimizando Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Limpiar cachés anteriores
php artisan cache:clear

echo "✅ Configuración de Railway completada"
echo "🎉 La aplicación está lista para funcionar"
