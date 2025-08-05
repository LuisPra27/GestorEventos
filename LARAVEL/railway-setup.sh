#!/bin/bash

# Railway Setup Script - Gestor de Eventos
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
    echo "📦 Ejecutando migraciones de base de datos..."

    # Intentar conectar a la base de datos
    until php artisan migrate --force 2>/dev/null; do
        echo "⏳ Esperando que la base de datos esté lista..."
        sleep 5
    done

    echo "✅ Migraciones completadas"

    # Ejecutar seeders solo en primera instalación
    if php artisan migrate:status | grep -q "No migrations found"; then
        echo "🌱 Ejecutando seeders iniciales..."
        php artisan db:seed --force || echo "⚠️  Seeders no ejecutados (puede ser normal)"
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
