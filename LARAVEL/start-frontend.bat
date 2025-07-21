@echo off
echo ====================================
echo    INICIANDO FRONTEND
echo ====================================
echo.
echo Directorio: %~dp0
echo Puerto: 3000
echo.
echo Verificando si Python esta disponible...
python --version >nul 2>&1
if %errorlevel% == 0 (
    echo Usando Python HTTP Server
    echo Frontend disponible en: http://localhost:3000
    echo Pagina principal: http://localhost:3000/index.html
    echo Login: http://localhost:3000/login.html
    echo Panel de pruebas: http://localhost:3000/test-dashboards.html
    echo.
    echo Presiona Ctrl+C para detener el servidor
    python -m http.server 3000
) else (
    echo Python no encontrado. Verifica que este instalado.
    echo.
    echo Alternativas:
    echo 1. Instalar Python desde python.org
    echo 2. Usar Live Server extension en VS Code
    echo 3. Usar Node.js: npx http-server -p 3000
    echo.
    pause
)
