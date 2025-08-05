# 🚀 SOLUCION AL ERROR: "deploy.restartPolicyType: Invalid input"

## ✅ ERROR CORREGIDO
El archivo `railway.toml` ha sido corregido. El problema era una configuración inválida.

## 📋 ORDEN CORRECTO DE DESPLIEGUE:

### **1. CREAR POSTGRESQL PRIMERO**
```
Railway Dashboard → "New" → "Database" → "PostgreSQL"
- No requiere configuración adicional
- Railway generará automáticamente las variables PG*
```

### **2. CREAR BACKEND (Laravel)**
```
Railway Dashboard → "New" → "GitHub Repo" → Tu repositorio

CONFIGURACIÓN CRÍTICA:
- Root Directory: "LARAVEL"
- Service Name: "gestor-eventos-backend"
- Connect Database: Seleccionar la PostgreSQL creada en paso 1

VARIABLES AUTOMÁTICAS (Railway las crea):
- PGHOST, PGPORT, PGDATABASE, PGUSER, PGPASSWORD
- PORT (Railway lo asigna automáticamente)
```

### **3. CREAR FRONTEND (Nginx)**
```
Railway Dashboard → "New" → "GitHub Repo" → Mismo repositorio

CONFIGURACIÓN CRÍTICA:
- Root Directory: "/" (raíz del proyecto)
- Service Name: "gestor-eventos-frontend"

NO conectar base de datos al frontend.
```

## 🔧 CONFIGURACIONES ESPECÍFICAS POR SERVICIO:

### **Backend (LARAVEL/):**
- ✅ Usa: `LARAVEL/railway.toml`
- ✅ Usa: `LARAVEL/Dockerfile`
- ✅ Health check: `/api/health`

### **Frontend (/):**
- ✅ Usa: `railway.toml` (raíz)
- ✅ Usa: `Dockerfile` (raíz)
- ✅ Health check: `/health`

## ⚠️ TIPS IMPORTANTES:

1. **Desplegar en orden**: PostgreSQL → Backend → Frontend
2. **Root Directory es CRÍTICO**: LARAVEL/ vs /
3. **Solo el backend se conecta a PostgreSQL**
4. **Esperar que cada servicio termine antes del siguiente**

## 🔍 VERIFICAR DESPUÉS DEL DESPLIEGUE:

### **Backend:**
- URL: `https://[backend-name].railway.app/api/health`
- Debe mostrar: `{"status":"ok","database":"connected"}`

### **Frontend:**
- URL: `https://[frontend-name].railway.app/health`
- Debe mostrar: `healthy`

## 🚨 SI SIGUES TENIENDO PROBLEMAS:

1. **Verificar logs**: Railway Dashboard → Service → Logs
2. **Verificar variables**: Railway Dashboard → Service → Variables
3. **Verificar Root Directory**: Railway Dashboard → Service → Settings

¡Ahora el despliegue debería funcionar correctamente!
