# 🚀 Guía Completa de Despliegue en Railway

## Gestor de Eventos - Configuración para Railway

### 📋 Prerrequisitos
1. Cuenta en [Railway.app](https://railway.app)
2. Repositorio del proyecto en GitHub
3. Proyecto configurado localmente

### 🗄️ Estructura de Servicios en Railway

El proyecto requiere **3 servicios** en Railway:

1. **PostgreSQL Database** (Add-on)
2. **Backend (Laravel)** 
3. **Frontend (Nginx)**

---

## 🚀 Pasos de Despliegue

### 1. Crear Proyecto en Railway

1. Conecta tu repositorio de GitHub a Railway
2. Crea un nuevo proyecto desde el repositorio

### 2. Agregar PostgreSQL

1. En el dashboard del proyecto, clic en "Add Service"
2. Selecciona "Database" → "PostgreSQL"
3. Railway creará automáticamente las variables:
   - `PGHOST`
   - `PGPORT` 
   - `PGDATABASE`
   - `PGUSER`
   - `PGPASSWORD`

### 3. Configurar Backend (Laravel)

1. **Crear servicio Backend:**
   - "Add Service" → "GitHub Repo"
   - Selecciona tu repositorio
   - **Root Directory:** `LARAVEL/`
   - Railway detectará automáticamente el Dockerfile

2. **Conectar PostgreSQL:**
   - En el servicio Backend, ve a "Variables"
   - Clic en "Connect" y selecciona el PostgreSQL creado
   - Las variables se conectarán automáticamente

3. **Variables adicionales** (opcional):
   ```
   APP_ENV=production
   APP_DEBUG=false
   APP_NAME=Gestor de Eventos
   ```

### 4. Configurar Frontend (Nginx)

1. **Crear servicio Frontend:**
   - "Add Service" → "GitHub Repo"
   - Selecciona el mismo repositorio
   - **Root Directory:** `/` (raíz del proyecto)
   - Railway usará el Dockerfile en la raíz

2. **Configurar variables** (después del despliegue):
   - Anota la URL del backend: `https://[backend-name].railway.app`
   - Esta URL se usará en el frontend

---

## 🔧 Configuración Post-Despliegue

### Paso 1: Obtener URLs de los Servicios

Después del despliegue, tendrás:
- **Frontend:** `https://[frontend-name].railway.app`
- **Backend:** `https://[backend-name].railway.app`

### Paso 2: Configurar Conexión Frontend-Backend

1. Visita tu frontend en el navegador
2. En la primera carga, el sistema te pedirá la URL del backend
3. Ingresa: `https://[backend-name].railway.app/api`
4. La configuración se guardará automáticamente

### Paso 3: Verificar Funcionamiento

1. **Health Check Backend:** `https://[backend-name].railway.app/api/health`
2. **Health Check Frontend:** `https://[frontend-name].railway.app/health`

---

## 🛠️ Características Incluidas

✅ **Configuración automática de PostgreSQL**
- Variables de entorno automáticas
- Migraciones automáticas en el backend

✅ **Health checks**
- Monitoreo de ambos servicios
- Detección automática de problemas

✅ **Optimización para Railway**
- Builds optimizados
- Configuración de red adecuada

✅ **Gestión de errores**
- Logs detallados
- Páginas de error personalizadas

✅ **Seguridad**
- Configuración HTTPS automática
- Headers de seguridad

---

## 🐛 Solución de Problemas

### Backend no conecta a PostgreSQL
```bash
# Verificar variables en Railway
echo $PGHOST $PGPORT $PGDATABASE
```

### Frontend no conecta al Backend
1. Verificar URL del backend en el navegador
2. Comprobar que ambos servicios estén corriendo
3. Revisar logs en Railway dashboard

### Migraciones fallan
1. Verificar que PostgreSQL esté corriendo
2. Comprobar variables de conexión
3. Revisar logs del backend

---

## 📝 Variables de Entorno Importantes

### Backend (Laravel)
- `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD` (automáticas)
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY` (se genera automáticamente)

### Frontend (Nginx)
- No requiere variables especiales
- La URL del backend se configura en tiempo de ejecución

---

## 🔄 Actualizaciones

Para actualizar el proyecto:
1. Haz push a tu repositorio de GitHub
2. Railway desplegará automáticamente los cambios
3. Las migraciones se ejecutarán automáticamente

---

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs en Railway dashboard
2. Verifica que todos los servicios estén corriendo
3. Comprueba las URLs de health check

**URLs de Health Check:**
- Backend: `/api/health`
- Frontend: `/health`

---

¡Tu aplicación Gestor de Eventos estará lista para usar en Railway! 🎉
