# 🚀 Guía de Despliegue en Railway - Paso a Paso

## ✅ Ya completado: GitHub conectado a Railway

## 📋 Pasos siguientes (en este orden):

### **1. CREAR BASE DE DATOS POSTGRESQL**
```
1. En Railway Dashboard → "New" → "Database" → "PostgreSQL"
2. Railway creará automáticamente:
   - PGHOST
   - PGPORT
   - PGDATABASE
   - PGUSER
   - PGPASSWORD
   - DATABASE_URL
```

### **2. CREAR SERVICIO BACKEND (Laravel)**
```
1. En Railway → "New" → "GitHub Repo" → Seleccionar tu repositorio
2. ⚠️ IMPORTANTE: Configurar "Root Directory" = "LARAVEL"
3. Railway detectará automáticamente el Dockerfile
4. En "Variables":
   - Conectar la base de datos PostgreSQL (Railway lo hace automático)
   - Agregar: PORT = ${{ PORT }} (Railway lo maneja)
```

### **3. CREAR SERVICIO FRONTEND (Nginx)**
```
1. En Railway → "New" → "GitHub Repo" → Mismo repositorio
2. ⚠️ IMPORTANTE: Configurar "Root Directory" = "/" (raíz)
3. Railway usará el Dockerfile en la raíz
4. En "Variables":
   - No necesita configuración adicional
```

### **4. CONFIGURAR CONEXIÓN FRONTEND-BACKEND**
```
Después del despliegue:
1. Copia la URL del backend: https://[backend-name].railway.app
2. Actualiza en frontend: js/railway-config.js
3. O usa la función automática que detecta las URLs
```

### **5. VERIFICAR FUNCIONAMIENTO**
```
URLs para verificar:
- Backend Health: https://[backend].railway.app/api/health
- Frontend Health: https://[frontend].railway.app/health
- Aplicación: https://[frontend].railway.app
```

## 🔧 Variables de Entorno Importantes

### **Backend (se configuran automáticamente):**
- `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD`
- `PORT` (Railway lo asigna)
- `RAILWAY_PUBLIC_DOMAIN`

### **Opcionales para agregar manualmente:**
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_NAME="Gestor de Eventos"`

## ⚠️ Problemas Comunes y Soluciones

### **Backend no se conecta a PostgreSQL:**
```
- Verificar que PostgreSQL esté conectado al servicio backend
- Revisar logs: Railway Dashboard → Backend Service → Logs
- Las variables PG* deben aparecer automáticamente
```

### **Frontend no se conecta al Backend:**
```
- Verificar URLs en railway-config.js
- Usar la función testBackendConnection() en el navegador
- Revisar CORS si es necesario
```

### **Migraciones no se ejecutan:**
```
- Verificar logs del backend
- Las migraciones se ejecutan automáticamente en railway-setup.sh
- Pueden tardar unos minutos en la primera ejecución
```

## 🎯 Orden de Despliegue Recomendado:
1. **PostgreSQL** (primero)
2. **Backend** (segundo, conectar a PostgreSQL)
3. **Frontend** (último)

## 📞 URLs Finales Esperadas:
- **Frontend**: `https://gestoreventos-frontend-production.railway.app`
- **Backend**: `https://gestoreventos-backend-production.railway.app`
- **API**: `https://gestoreventos-backend-production.railway.app/api/`

¡Railway manejará automáticamente SSL, dominios y variables de entorno!
