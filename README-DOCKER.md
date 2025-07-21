# 🐳 Sistema Docker - Gestor de Eventos

Sistema completo de gestión de eventos containerizado con Docker, compuesto por tres contenedores independientes e interconectados.

## 🏗️ Arquitectura

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   FRONTEND      │    │    BACKEND      │    │   DATABASE      │
│   (Nginx)       │◄──►│   (Laravel)     │◄──►│ (PostgreSQL)    │
│   Puerto: 80    │    │   Puerto: 8000  │    │   Puerto: 5432  │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### 📦 Contenedores

1. **🌐 Frontend (Nginx)**: `gestor_eventos_frontend`
   - Sirve la aplicación web estática
   - Proxy reverso para el backend
   - Puerto: 80 (HTTP)

2. **⚡ Backend (Laravel)**: `gestor_eventos_backend`
   - API REST con Laravel
   - Lógica de negocio
   - Puerto: 8000

3. **🗄️ Base de Datos (PostgreSQL)**: `gestor_eventos_db`
   - Almacenamiento persistente
   - Puerto: 5432

## 🚀 Inicio Rápido

### Opción 1: Script Windows (Recomendado)
```bash
docker-manager.bat
```

### Opción 2: Docker Compose Manual
```bash
# Construir e iniciar todos los contenedores
docker-compose up -d --build

# Ver estado
docker-compose ps

# Ver logs
docker-compose logs -f
```

## 🌐 Acceso a Servicios

| Servicio | URL | Descripción |
|----------|-----|-------------|
| **Aplicación Web** | http://localhost | Interfaz principal |
| **Estado del Sistema** | http://localhost/status | Monitor de servicios |
| **API Backend** | http://localhost:8000 | API REST |
| **Health Check** | http://localhost:8000/api/health | Estado del backend |

## 🔧 Comandos Útiles

### Gestión de Contenedores
```bash
# Iniciar sistema
docker-compose up -d

# Detener sistema
docker-compose down

# Reiniciar un servicio específico
docker-compose restart [service_name]

# Ver logs de un servicio
docker-compose logs -f [service_name]
```

### Monitoreo
```bash
# Estado de todos los contenedores
docker-compose ps

# Recursos utilizados
docker stats

# Inspeccionar un contenedor
docker inspect gestor_eventos_frontend
```

## 🧪 Pruebas de Resistencia

### Simular Fallo del Backend
```bash
# Detener el backend
docker-compose stop backend

# Verificar impacto en: http://localhost/status
# El frontend seguirá funcionando pero mostrará errores de API

# Reiniciar backend
docker-compose start backend
```

### Simular Fallo de Base de Datos
```bash
# Detener la base de datos
docker-compose stop database

# El backend mostrará errores de conexión
# La aplicación web reflejará estos errores

# Reiniciar base de datos
docker-compose start database
```

### Simular Fallo del Frontend
```bash
# Detener el frontend
docker-compose stop frontend

# La aplicación web no estará disponible
# Pero backend y base de datos seguirán funcionando

# Verificar backend directamente: http://localhost:8000/api/health

# Reiniciar frontend
docker-compose start frontend
```

## 🛠️ Troubleshooting

### Problemas Comunes

#### 1. Puerto ya en uso
```bash
# Verificar qué proceso usa el puerto
netstat -ano | findstr :80
netstat -ano | findstr :8000
netstat -ano | findstr :5432

# Detener el proceso o cambiar puertos en docker-compose.yml
```

#### 2. Contenedor no inicia
```bash
# Ver logs detallados
docker-compose logs [service_name]

# Reconstruir contenedor
docker-compose build --no-cache [service_name]
```

#### 3. Base de datos no conecta
```bash
# Verificar estado del contenedor
docker-compose exec database pg_isready -U postgres

# Conectar manualmente
docker-compose exec database psql -U postgres -d eventos
```

#### 4. Limpiar sistema completo
```bash
# ⚠️ CUIDADO: Elimina TODOS los datos
docker-compose down -v --rmi all
docker system prune -f
```

## 📊 Monitoreo Avanzado

### Health Checks
Cada contenedor tiene health checks configurados:

```yaml
# Backend
curl -f http://localhost:8000/api/health

# Frontend
curl -f http://localhost

# Database
pg_isready -U postgres -d eventos
```

### Logs Centralizados
```bash
# Ver todos los logs
docker-compose logs

# Filtrar por servicio
docker-compose logs backend

# Seguir logs en tiempo real
docker-compose logs -f --tail=100
```

## 🔐 Configuración de Seguridad

### Variables de Entorno
Las credenciales están en `LARAVEL/.env`:
- Database: `postgres/luis123`
- Database name: `eventos`
- App Key: Auto-generada

### Red Docker
Los contenedores se comunican a través de una red privada `gestor_network`.

## 📁 Estructura de Archivos

```
GestorEventos/
├── docker-compose.yml          # Orquestación principal
├── nginx.conf                  # Configuración Nginx
├── system-status.html          # Monitor del sistema
├── docker-manager.bat          # Script de gestión (Windows)
├── LARAVEL/
│   ├── Dockerfile             # Imagen del backend
│   └── .env                   # Configuración Laravel
└── logs/                      # Logs de Nginx
```

## 🎯 Objetivos Cumplidos

✅ **Tres contenedores independientes**: Frontend, Backend, Database  
✅ **Interconexión funcional**: Red Docker privada  
✅ **Sistema completo operativo**: Aplicación web funcional  
✅ **Demostración de dependencias**: Fallo de uno afecta el sistema  
✅ **Monitoreo en tiempo real**: Página de estado del sistema  
✅ **Fácil gestión**: Scripts automatizados  
✅ **Documentación completa**: Instrucciones detalladas  

## 📞 Soporte

Para problemas o mejoras:
1. Revisa los logs: `docker-compose logs`
2. Verifica el estado: `docker-compose ps`
3. Reinicia servicios: `docker-compose restart [service]`
