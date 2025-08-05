# 🎪 Gestor de Eventos - Railway Deployment

Sistema completo de gestión de eventos desplegado en Railway.

## 🌐 Enlaces de Producción

- **Frontend**: [Tu URL de Frontend en Railway]
- **Backend API**: `https://back-end-production-fca9.up.railway.app/api`
- **Estado del Sistema**: [Tu URL]/system-status.html

## 🏗️ Arquitectura del Sistema

### Servicios en Railway
1. **PostgreSQL Database** - Base de datos principal
2. **Laravel Backend** - API REST (Puerto dinámico de Railway)
3. **Frontend** - Archivos estáticos servidos por Railway

### Stack Tecnológico
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: Laravel 11 + Sanctum Authentication
- **Base de Datos**: PostgreSQL 15
- **Hosting**: Railway.app

## 🚀 Funcionalidades del Sistema

### Autenticación y Roles
- ✅ Sistema de login/registro
- ✅ 3 tipos de usuario: Cliente, Empleado, Gerente
- ✅ Verificación automática de roles cada 10 segundos
- ✅ Logout automático al cambiar roles
- ✅ Detección inmediata de cambios de permisos

### Dashboard Cliente
- ✅ Crear nuevos eventos
- ✅ Ver eventos propios
- ✅ Seguimiento de estado de eventos
- ✅ Estadísticas personales

### Dashboard Empleado
- ✅ Ver eventos asignados
- ✅ Cambiar estado de eventos
- ✅ Agregar seguimientos y comentarios
- ✅ Filtros por estado (pendiente, en progreso, completado)

### Dashboard Gerente
- ✅ Gestión completa de usuarios
- ✅ Gestión de servicios
- ✅ Asignación de empleados a eventos
- ✅ Reportes y estadísticas globales
- ✅ Control de estados de cuentas

## � Configuración de Desarrollo Local

### Prerrequisitos
- PHP 8.0+ con Composer
- Node.js (opcional, para desarrollo frontend)
- PostgreSQL local (opcional)

### Backend (Laravel)
```bash
cd LARAVEL
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve --host=0.0.0.0 --port=8000
```

### Frontend
Servir archivos estáticos desde la raíz del proyecto en un servidor web.

## 🚢 Deployment en Railway

### Variables de Entorno Requeridas

**Backend (LARAVEL/):**
```env
APP_NAME="Gestor de Eventos"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://back-end-production-fca9.up.railway.app

DB_CONNECTION=pgsql
DB_HOST=${{ PGHOST }}
DB_PORT=${{ PGPORT }}
DB_DATABASE=${{ PGDATABASE }}
DB_USERNAME=${{ PGUSER }}
DB_PASSWORD=${{ PGPASSWORD }}

SESSION_DRIVER=cookie
SANCTUM_STATEFUL_DOMAINS=tu-frontend-domain.railway.app
```

### Estructura de Servicios Railway

1. **PostgreSQL Database**
   - Creado como add-on de Railway
   - Variables automáticas: PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD

2. **Backend Service**
   - Root Directory: `LARAVEL`
   - Build Command: Automático (Dockerfile)
   - Start Command: Automático
   - Puerto: `$PORT` (automático de Railway)

3. **Frontend Service** 
   - Root Directory: `/` (raíz del proyecto)
   - Archivos estáticos: index.html, dashboard-*.html, css/, js/, images/

## � Estructura del Proyecto

```
GestorEventos/
├── README.md                   # Este archivo
├── index.html                  # Página principal
├── login.html                  # Página de login
├── register.html               # Página de registro
├── dashboard-cliente.html      # Panel cliente
├── dashboard-empleado.html     # Panel empleado  
├── dashboard-gerente.html      # Panel gerente
├── system-status.html          # Estado del sistema
├── railway.toml               # Configuración Railway
├── css/
│   └── style.css              # Estilos principales
├── js/
│   ├── api-utils.js          # Utilidades API
│   └── script.js             # JavaScript principal
├── images/                   # Recursos gráficos
└── LARAVEL/                  # Backend Laravel
    ├── Dockerfile           # Contenedor para Railway
    ├── railway.toml         # Config Railway backend
    ├── app/                 # Código Laravel
    ├── database/            # Migraciones y seeders
    └── ...                  # Resto de Laravel
```

## 🔐 Seguridad

### Autenticación
- Tokens Sanctum para API
- Verificación continua de sesiones
- Logout automático ante cambios de rol
- Protección CSRF

### Roles y Permisos
- **Cliente (rol_id: 1)**: Solo gestión de eventos propios
- **Empleado (rol_id: 2)**: Eventos asignados + seguimientos
- **Gerente (rol_id: 3)**: Acceso completo al sistema

## 🚨 Monitoreo y Estado

### Health Checks
- `/api/health` - Estado general del backend
- `/api/database/status` - Estado de la base de datos
- `system-status.html` - Dashboard de monitoreo

### Logs
- Logs de Railway disponibles en el dashboard
- Seguimiento de errores en tiempo real
- Métricas de performance automáticas

## 📞 Soporte

Para problemas técnicos:
1. Verificar estado en `system-status.html`
2. Revisar logs en Railway Dashboard
3. Verificar variables de entorno
4. Comprobar conectividad de base de datos

---

**� Desplegado en Railway | 🛡️ Seguro por defecto | ⚡ Escalable automáticamente**
