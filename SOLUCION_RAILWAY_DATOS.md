# 🚨 SOLUCIÓN: Railway Borrando Datos PostgreSQL

## ❌ **¿Por qué sigues perdiendo datos?**

**Railway SÍ guarda los datos de PostgreSQL**, pero tu script de migración los está eliminando por esta razón:

### 🔍 **Problema Identificado:**
Tu `railway-setup.sh` estaba ejecutando **SIEMPRE**:
```bash
php artisan migrate --force  # ⚠️ PELIGROSO - Puede eliminar datos
php artisan db:seed --force   # ⚠️ Solo debe ejecutarse UNA VEZ
```

## ✅ **Solución Implementada**

### 1. **Script Corregido**
Ahora el script:
- ✅ Verifica si ya hay datos antes de migrar
- ✅ Solo ejecuta seeders en instalación inicial
- ✅ Usa el comando `migrate:safe` que preserva datos

### 2. **Variables de Railway a Configurar**

En tu dashboard de Railway, ve a tu servicio Laravel → Variables y agrega:

```env
SAFE_MIGRATION_MODE=true
FRESH_INSTALL=false
```

**IMPORTANTE:** 
- Para el **primer deploy**: `FRESH_INSTALL=true`
- **Después del primer deploy**: `FRESH_INSTALL=false`

### 3. **Verificar Conexión PostgreSQL**

En Railway:
1. Ve a tu proyecto
2. Asegúrate que tienes **2 servicios**:
   - ✅ Tu aplicación Laravel
   - ✅ PostgreSQL Database

3. Conecta PostgreSQL a Laravel:
   - Click en PostgreSQL → Connect → Tu servicio Laravel

## 🔧 **Pasos para Resolver**

### **Paso 1: Verificar Estado Actual**
```bash
# En Railway logs, deberías ver:
✅ PostgreSQL detectado: [host]:[port]
📦 Iniciando configuración de base de datos...
🔧 Ejecutando migraciones seguras...
```

### **Paso 2: Si Aún Pierdes Datos**
1. **Primera vez**: Cambiar `FRESH_INSTALL=true` en Railway
2. **Hacer push y deploy**
3. **Inmediatamente**: Cambiar `FRESH_INSTALL=false`

### **Paso 3: Verificar en Logs**
Busca en Railway logs:
```bash
📊 Datos existentes detectados (X usuarios)
```

## 🎯 **¿Cómo Funciona Ahora?**

### **Primer Deploy (FRESH_INSTALL=true)**
```
🆕 Base de datos vacía detectada
📦 Ejecutando todas las migraciones
🌱 Ejecutando seeders iniciales
✅ Base de datos inicializada
```

### **Redeploys Posteriores (FRESH_INSTALL=false)**
```
📊 Datos existentes detectados (5 usuarios)
🔧 Solo aplicando migraciones pendientes
✅ Datos preservados
```

## 💾 **Persistencia de Datos en Railway**

Railway guarda los datos PostgreSQL en:
- **Volumen persistente**: Los datos NO se eliminan entre deploys
- **Base de datos separada**: Independiente de tu aplicación
- **Backup automático**: Railway hace backups regulares

## 🆘 **Si Sigues Teniendo Problemas**

### **Debug en Railway:**
1. Ve a Deployments → Logs
2. Busca líneas con:
   - `📦 Iniciando configuración de base de datos`
   - `📊 Datos existentes detectados`
   - `✅ Migraciones completadas exitosamente`

### **Verificar Variables:**
En Railway Variables, debe aparecer:
- `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD` (automáticas)
- `SAFE_MIGRATION_MODE=true` (manual)
- `FRESH_INSTALL=false` (manual, después del primer deploy)

### **Comando Manual de Emergencia:**
Si necesitas restaurar datos, en Railway console:
```bash
php artisan migrate:safe --force
```

## ✅ **Resultado Esperado**

Con esta configuración:
- ✅ **Primer deploy**: Crea todo desde cero
- ✅ **Redeploys**: Preserva datos existentes
- ✅ **Nuevas migraciones**: Se aplican sin borrar datos
- ✅ **Logs claros**: Puedes ver exactamente qué hace

---

**¡Los datos de PostgreSQL en Railway SON PERSISTENTES! El problema era el script de migraciones, no Railway.**
