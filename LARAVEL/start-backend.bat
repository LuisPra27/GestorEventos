@echo off
echo ====================================
echo    INICIANDO BACKEND LARAVEL
echo ====================================
echo.
echo Directorio: %~dp0LARAVEL
echo Puerto: 8000
echo.
cd /d "%~dp0LARAVEL"
echo Ejecutando: php artisan serve --host=0.0.0.0 --port=8000
echo.
echo Presiona Ctrl+C para detener el servidor
echo Backend disponible en: http://localhost:8000
echo API disponible en: http://localhost:8000/api
echo.
php artisan serve --host=0.0.0.0 --port=8000
pause
