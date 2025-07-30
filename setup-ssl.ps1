# Script para configurar Gestor de Eventos con SSL
Write-Host "🔒 Configurando Gestor de Eventos con SSL..." -ForegroundColor Green

# Detener contenedores actuales si existen
Write-Host "📦 Deteniendo contenedores existentes..." -ForegroundColor Yellow
docker-compose down 2>$null

# Construir y levantar con SSL
Write-Host "🏗️ Construyendo contenedores con SSL..." -ForegroundColor Yellow
docker-compose -f docker-compose-ssl.yml up --build -d

Write-Host "⏳ Esperando que los servicios se inicializen..." -ForegroundColor Yellow
Start-Sleep -Seconds 30

# Verificar estado
Write-Host "🔍 Verificando estado de los contenedores..." -ForegroundColor Yellow
docker-compose -f docker-compose-ssl.yml ps

Write-Host ""
Write-Host "✅ ¡Gestor de Eventos con SSL está listo!" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Accesos disponibles:" -ForegroundColor Cyan
Write-Host "   HTTP:  http://localhost (redirige a HTTPS)" -ForegroundColor White
Write-Host "   HTTPS: https://localhost (SSL habilitado)" -ForegroundColor White
Write-Host "   API:   https://localhost/api/" -ForegroundColor White
Write-Host "   Estado: https://localhost/status" -ForegroundColor White
Write-Host ""
Write-Host "⚠️  NOTA: El certificado es autofirmado, acepta la advertencia de seguridad del navegador" -ForegroundColor Yellow
Write-Host ""
Write-Host "📋 Comandos útiles:" -ForegroundColor Cyan
Write-Host "   Ver logs: docker-compose -f docker-compose-ssl.yml logs -f" -ForegroundColor White
Write-Host "   Detener:  docker-compose -f docker-compose-ssl.yml down" -ForegroundColor White
Write-Host "   Estado:   docker-compose -f docker-compose-ssl.yml ps" -ForegroundColor White
