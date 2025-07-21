@echo off
title Gestor de Eventos - Sistema Docker

echo.
echo ========================================
echo 🐳 GESTOR DE EVENTOS - SISTEMA DOCKER
echo ========================================
echo.

:menu
echo Selecciona una opción:
echo.
echo 1. 🚀 Iniciar sistema completo
echo 2. 🛑 Detener sistema
echo 3. 📊 Ver estado de contenedores
echo 4. 📝 Ver logs en tiempo real
echo 5. 🔧 Reiniciar un servicio específico
echo 6. 🧹 Limpiar sistema (eliminar todo)
echo 7. 🌐 Abrir aplicaciones en navegador
echo 8. ❌ Salir
echo.

set /p choice=Ingresa tu opción (1-8): 

if "%choice%"=="1" goto start_system
if "%choice%"=="2" goto stop_system
if "%choice%"=="3" goto show_status
if "%choice%"=="4" goto show_logs
if "%choice%"=="5" goto restart_service
if "%choice%"=="6" goto clean_system
if "%choice%"=="7" goto open_apps
if "%choice%"=="8" goto exit
goto menu

:start_system
echo.
echo 🚀 Iniciando sistema...
docker-compose up -d --build
echo.
echo ✅ Sistema iniciado! Servicios disponibles:
echo   Frontend:  http://localhost
echo   Backend:   http://localhost:8000
echo   Estado:    http://localhost/status
echo.
pause
goto menu

:stop_system
echo.
echo 🛑 Deteniendo sistema...
docker-compose down
echo ✅ Sistema detenido!
echo.
pause
goto menu

:show_status
echo.
echo 📊 Estado de los contenedores:
docker-compose ps
echo.
echo 🔍 Salud de los servicios:
docker-compose exec backend curl -f http://localhost:8000/api/health 2>nul && echo Backend: ✅ Online || echo Backend: ❌ Offline
docker-compose exec frontend curl -f http://localhost 2>nul && echo Frontend: ✅ Online || echo Frontend: ❌ Offline
docker-compose exec database pg_isready -U postgres 2>nul && echo Database: ✅ Online || echo Database: ❌ Offline
echo.
pause
goto menu

:show_logs
echo.
echo 📝 Mostrando logs (Ctrl+C para salir)...
docker-compose logs -f
pause
goto menu

:restart_service
echo.
echo Servicios disponibles:
echo 1. database (PostgreSQL)
echo 2. backend (Laravel)
echo 3. frontend (Nginx)
echo.
set /p service_choice=Selecciona servicio (1-3): 

if "%service_choice%"=="1" set service_name=database
if "%service_choice%"=="2" set service_name=backend
if "%service_choice%"=="3" set service_name=frontend

if defined service_name (
    echo 🔧 Reiniciando %service_name%...
    docker-compose restart %service_name%
    echo ✅ %service_name% reiniciado!
) else (
    echo ❌ Opción inválida
)
echo.
pause
goto menu

:clean_system
echo.
echo ⚠️  ADVERTENCIA: Esto eliminará todos los contenedores, volúmenes e imágenes!
set /p confirm=¿Estás seguro? (s/N): 

if /i "%confirm%"=="s" (
    echo 🧹 Limpiando sistema...
    docker-compose down -v --rmi all
    docker system prune -f
    echo ✅ Sistema limpiado completamente!
) else (
    echo ❌ Operación cancelada
)
echo.
pause
goto menu

:open_apps
echo.
echo 🌐 Abriendo aplicaciones en navegador...
start http://localhost
start http://localhost/status
start http://localhost:8000/api/health
echo ✅ Aplicaciones abiertas!
echo.
pause
goto menu

:exit
echo.
echo 👋 ¡Hasta luego!
exit
