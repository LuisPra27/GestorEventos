#  GESTOR DE EVENTOS - SISTEMA COMPLETO

##  Descripción
Sistema de gestión de eventos con backend Laravel y frontend HTML/CSS/JavaScript.

##  Arquitectura
- **Backend**: Laravel 12 con Sanctum (API REST)
- **Frontend**: HTML/CSS/JavaScript (SPA-style)
- **Base de Datos**: PostgreSQL
- **Autenticación**: Tokens Sanctum

##  Comandos de Ejecución

### Backend Laravel
```bash
cd LARAVEL
php artisan serve --host=0.0.0.0 --port=8000
```
**O usar script:** `start-backend.bat`

### Frontend
```bash
# Opción 1: Python
python -m http.server 3000

# Opción 2: Node.js
npx http-server -p 3000

# Opción 3: VS Code Live Server Extension
```
**O usar script:** `start-frontend.bat`

##  URLs de Acceso
- **Backend API**: http://localhost:8000
- **Frontend**: http://localhost:3000
- **Login**: http://localhost:3000/login.html
- **Registro**: http://localhost:3000/register.html
- **Panel de Pruebas**: http://localhost:3000/test-dashboards.html

##  Cuentas de Prueba
- **Gerente**: admin@eventos.com / password
- **Empleado**: empleado@eventos.com / password

##  Estructura de Archivos
```
 LARAVEL/                 # Backend Laravel
 js/
    api-utils.js        # Utilidades para API
 css/                    # Estilos
 images/                 # Imágenes
 index.html              # Página principal
 login.html              # Login
 register.html           # Registro
 dashboard-cliente.html  # Dashboard cliente
 dashboard-empleado.html # Dashboard empleado
 dashboard-gerente.html  # Dashboard gerente
 test-dashboards.html    # Panel de pruebas
 start-backend.bat       # Script para backend
 start-frontend.bat      # Script para frontend
```

##  Endpoints API
- POST `/api/login` - Iniciar sesión
- POST `/api/register` - Registrar usuario
- POST `/api/logout` - Cerrar sesión
- GET `/api/me` - Información del usuario
- GET `/api/users` - Listar usuarios (gerente)
- GET `/api/services` - Listar servicios
- GET `/api/events` - Listar eventos

##  Roles y Permisos
- **Cliente (1)**: Crear y gestionar sus eventos
- **Empleado (2)**: Ver eventos asignados y actualizar estado
- **Gerente (3)**: Acceso completo al sistema

##  Desarrollo
1. Ejecutar backend Laravel
2. Ejecutar servidor web para frontend
3. Abrir navegador en URL del frontend
4. Usar cuentas de prueba para login

¡Sistema listo para usar! 
