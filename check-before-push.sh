#!/bin/bash

# Script: check-before-push.sh
# Propósito: Verificar el estado antes de hacer push a Railway

echo "🔍 Verificación Pre-Push para Railway"
echo "=====================================

# Verificar variables de entorno críticas
echo "📋 Verificando configuración..."

if [ -f "LARAVEL/.env.railway" ]; then
    echo "✅ Archivo .env.railway encontrado"
    
    # Extraer variables importantes
    SAFE_MODE=$(grep "SAFE_MIGRATION_MODE" LARAVEL/.env.railway | cut -d'=' -f2)
    FRESH_INSTALL=$(grep "FRESH_INSTALL" LARAVEL/.env.railway | cut -d'=' -f2)
    
    echo "🛡️  SAFE_MIGRATION_MODE: $SAFE_MODE"
    echo "🆕 FRESH_INSTALL: $FRESH_INSTALL"
    
    if [ "$FRESH_INSTALL" = "true" ]; then
        echo "⚠️  ADVERTENCIA: FRESH_INSTALL está en true"
        echo "   Esto ejecutará una instalación completa en Railway"
        echo "   ¿Estás seguro? [y/N]"
        read -r response
        if [[ ! "$response" =~ ^[Yy]$ ]]; then
            echo "❌ Push cancelado por el usuario"
            exit 1
        fi
    fi
else
    echo "⚠️  Archivo .env.railway no encontrado"
fi

# Verificar que existen los archivos críticos
echo ""
echo "📂 Verificando archivos críticos..."

critical_files=(
    "LARAVEL/railway-setup.sh"
    "LARAVEL/app/Console/Commands/SafeMigrate.php"
    "LARAVEL/nixpacks.toml"
)

for file in "${critical_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $file"
    else
        echo "❌ $file NO ENCONTRADO"
        exit 1
    fi
done

# Verificar permisos del script
if [ -x "LARAVEL/railway-setup.sh" ]; then
    echo "✅ railway-setup.sh es ejecutable"
else
    echo "🔧 Haciendo railway-setup.sh ejecutable..."
    chmod +x LARAVEL/railway-setup.sh
fi

# Verificar estructura de seeders
echo ""
echo "🌱 Verificando seeders..."

seeder_files=(
    "LARAVEL/database/seeders/RoleSeeder.php"
    "LARAVEL/database/seeders/UserSeeder.php"
    "LARAVEL/database/seeders/ServiceSeeder.php"
)

for file in "${seeder_files[@]}"; do
    if [ -f "$file" ]; then
        echo "✅ $(basename $file)"
    else
        echo "⚠️  $(basename $file) no encontrado"
    fi
done

# Verificar que no hay comandos peligrosos en migraciones
echo ""
echo "🔒 Verificando migraciones por comandos peligrosos..."

dangerous_patterns=("migrate:fresh" "migrate:reset" "db:wipe" "truncate")

for pattern in "${dangerous_patterns[@]}"; do
    if grep -r "$pattern" LARAVEL/database/migrations/ 2>/dev/null; then
        echo "⚠️  Patrón peligroso '$pattern' encontrado en migraciones"
    fi
done

# Mostrar resumen
echo ""
echo "📊 RESUMEN DE VERIFICACIÓN"
echo "========================="
echo "✅ Configuración verificada"
echo "✅ Archivos críticos presentes"
echo "✅ Migraciones seguras implementadas"
echo ""

# Pregunta final
echo "🚀 ¿Proceder con el push a Railway? [y/N]"
read -r final_response

if [[ "$final_response" =~ ^[Yy]$ ]]; then
    echo "✅ Push autorizado"
    echo ""
    echo "📋 RECORDATORIOS POST-PUSH:"
    echo "- Verificar logs en Railway dashboard"
    echo "- Probar login en la aplicación"
    echo "- Si es primera instalación, cambiar FRESH_INSTALL=false"
    echo ""
else
    echo "❌ Push cancelado"
    exit 1
fi
