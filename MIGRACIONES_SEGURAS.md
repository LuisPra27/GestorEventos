# 🛡️ Guía de Migraciones Seguras en Railway

Este documento explica cómo prevenir la pérdida de datos durante los re-despliegues en Railway.

## 🚨 Problema Identificado

Cada vez que se hace push a GitHub, Railway reconstruye las imágenes y ejecuta las migraciones, lo que puede causar pérdida de datos si no se maneja correctamente.

## ✅ Solución Implementada

Hemos implementado un sistema de **migraciones seguras** que:

1. **Detecta automáticamente** el estado de la base de datos
2. **Preserva los datos existentes** en re-despliegues
3. **Solo aplica migraciones pendientes** cuando hay datos
4. **Evita ejecutar seeders** en bases de datos con datos

## 🔧 Configuración en Railway

### Paso 1: Variables de Entorno

En el dashboard de Railway, agregar estas variables en la sección **Variables**:

```bash
# Configuración de seguridad
SAFE_MIGRATION_MODE=true
FRESH_INSTALL=false

# Laravel básico
APP_NAME="Gestor de Eventos"
APP_ENV=production
APP_DEBUG=false
APP_TIMEZONE=America/Mexico_City
```

### Paso 2: Primera Instalación

**Solo para el primer despliegue:**

1. Cambiar `FRESH_INSTALL=true` en Railway
2. Hacer el primer deploy
3. **Inmediatamente después** cambiar `FRESH_INSTALL=false`

### Paso 3: Configuración PostgreSQL

Railway configura automáticamente estas variables al conectar PostgreSQL:
- `PGHOST`, `PGPORT`, `PGDATABASE`, `PGUSER`, `PGPASSWORD`

## 🔄 Comportamiento del Sistema

### Primera Instalación (`FRESH_INSTALL=true`)
```
🆕 Detecta instalación fresca
📦 Ejecuta todas las migraciones
🌱 Ejecuta todos los seeders
✅ Base de datos completamente inicializada
```

### Re-despliegues (`FRESH_INSTALL=false`)
```
🔍 Analiza estado de la base de datos
📊 Detecta datos existentes
🛡️  Modo seguro activado
📝 Solo aplica migraciones pendientes
✅ Datos preservados
```

### Sin Datos (Recuperación)
```
🌱 Detecta tablas vacías
📦 Aplica migraciones
🌱 Ejecuta seeders para datos básicos
✅ Sistema restaurado
```

## 📋 Comandos Implementados

### Comando Artisan Personalizado
```bash
php artisan migrate:safe --force
```

Este comando:
- ✅ Verifica conexión a BD
- ✅ Analiza estado de tablas
- ✅ Aplica solo cambios necesarios
- ✅ Preserva datos existentes
- ✅ Genera logs de backup

### Script de Railway Mejorado
El `railway-setup.sh` ahora:
- ⏳ Espera conexión estable a BD
- 🔍 Usa detección inteligente
- 🛡️ Aplica migraciones seguras
- 📊 Genera información de backup

## 🚨 Advertencias Importantes

### ⚠️ NUNCA hacer esto en producción:
```bash
php artisan migrate:fresh  # ❌ Elimina todos los datos
php artisan migrate:reset  # ❌ Elimina todos los datos
php artisan db:wipe       # ❌ Elimina todos los datos
```

### ✅ SÍ hacer esto:
```bash
php artisan migrate:safe   # ✅ Migraciones seguras
php artisan migrate        # ✅ Solo si sabes lo que haces
```

## 🔧 Debugging

### Ver Estado de Migraciones
```bash
php artisan migrate:status
```

### Ver Tablas en BD
```bash
php artisan tinker
Schema::getTableListing()
DB::table('users')->count()
```

### Logs de Railway
Revisar los logs en el dashboard de Railway para ver:
- ✅ "Migraciones completadas exitosamente"
- 📊 "Base de datos con datos existentes"
- 🛡️ "Modo seguro activado"

## 🆘 Recuperación de Emergencia

Si algo sale mal:

1. **Revisar logs** en Railway dashboard
2. **Verificar variables** de entorno
3. **Re-ejecutar** con `FRESH_INSTALL=true` si es necesario
4. **Restaurar backup** si tienes uno

## 📞 Monitoreo

El sistema ahora genera:
- 📊 Logs de conteo de datos antes de migraciones
- 💾 Información de backup automática
- 🔍 Estado detallado en cada despliegue

## ✅ Verificación Post-Despliegue

Después de cada despliegue, verificar:

1. **Dashboard funciona**: `https://tu-app.railway.app/dashboard-gerente.html`
2. **Login funciona**: Usuario admin existe
3. **Datos preservados**: Eventos y usuarios anteriores existen
4. **Logs exitosos**: Sin errores en Railway logs

## 🎯 Resultado Esperado

Con esta configuración:
- ✅ **Primera instalación**: Datos completos y funcionales
- ✅ **Re-despliegues**: Datos preservados, solo actualizaciones
- ✅ **Sin interrupciones**: Sistema siempre funcional
- ✅ **Logs claros**: Fácil debugging en caso de problemas

---

## 📝 Notas de Desarrollo

- Archivo principal: `railway-setup.sh`
- Comando personalizado: `app/Console/Commands/SafeMigrate.php`
- Variables críticas: `SAFE_MIGRATION_MODE`, `FRESH_INSTALL`
- Comportamiento: Preservación automática de datos

**¡Recuerda cambiar `FRESH_INSTALL=false` después del primer despliegue!**
