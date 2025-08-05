# Railway Setup - Gestor de Eventos
# Script PowerShell optimizado para Railway

Write-Host "🚀 Configurando proyecto para Railway..." -ForegroundColor Green

# Verificar que estamos en el directorio correcto
if (-not (Test-Path "LARAVEL/artisan")) {
    Write-Host "❌ Error: Ejecuta este script desde la raíz del proyecto" -ForegroundColor Red
    exit 1
}

Write-Host "📦 Estructura del proyecto:" -ForegroundColor Yellow
Write-Host "  ├── Frontend (Nginx) - Raíz del proyecto"
Write-Host "  ├── Backend (Laravel) - Directorio LARAVEL/"
Write-Host "  └── PostgreSQL - Add-on de Railway"

Write-Host ""
Write-Host "🔧 Archivos de configuración creados:" -ForegroundColor Green
Write-Host "  ✅ Dockerfile (Frontend)"
Write-Host "  ✅ LARAVEL/Dockerfile (Backend)"
Write-Host "  ✅ railway.toml"
Write-Host "  ✅ LARAVEL/nixpacks.toml"
Write-Host "  ✅ RAILWAY_DEPLOYMENT_GUIDE.md"

Write-Host ""
Write-Host "📋 Próximos pasos para Railway:" -ForegroundColor Cyan
Write-Host "  1. Subir el código a GitHub"
Write-Host "  2. Conectar el repositorio a Railway"
Write-Host "  3. Crear servicio PostgreSQL"
Write-Host "  4. Crear servicio Backend (directorio: LARAVEL/)"
Write-Host "  5. Crear servicio Frontend (directorio: /)"

Write-Host ""
Write-Host "📚 Lee RAILWAY_DEPLOYMENT_GUIDE.md para instrucciones detalladas" -ForegroundColor Yellow

Write-Host ""
Write-Host "✅ ¡Proyecto listo para Railway!" -ForegroundColor Green
Write-Host "🔗 Railway Dashboard: https://railway.app/dashboard" -ForegroundColor Blue
