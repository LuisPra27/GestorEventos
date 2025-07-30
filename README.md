# 🔐 Gestor de Eventos - Versión SSL

Sistema de gestión de eventos con certificado SSL habilitado por defecto.

## 🚀 Inicio Rápido

### Prerrequisitos
- Docker Desktop instalado
- PowerShell (Windows) o Bash (Linux/Mac)

### 1. Levantar la aplicación
```bash
# Opción 1: Script automatizado (Windows)
.\setup.ps1

# Opción 2: Docker Compose manual
docker-compose up --build -d
```

### 2. Acceder a la aplicación
- **HTTPS (Recomendado):** https://localhost
- **HTTP (redirige a HTTPS):** http://localhost
- **API:** https://localhost/api/
- **Estado del sistema:** https://localhost/status
- **Ayuda SSL:** https://localhost/ssl-help

## 🛡️ Configuración SSL

### Certificado Auto-firmado
- El proyecto genera automáticamente un certificado SSL
- **Primera vez:** El navegador mostrará una advertencia de seguridad
- **Solución:** Hacer clic en "Avanzado" → "Continuar al sitio"

### Puertos Configurados
- **80 (HTTP):** Redirige automáticamente a HTTPS
- **443 (HTTPS):** Conexión segura con SSL
- **8000:** API Backend (Laravel)
- **5432:** Base de datos PostgreSQL

## 📁 Estructura del Proyecto

```
GestorEventos/
├── docker-compose.yml          # Configuración principal con SSL
├── Dockerfile.frontend         # Contenedor frontend con SSL
├── nginx.conf                  # Configuración Nginx con SSL
├── setup.ps1                   # Script de configuración automatizada
├── ssl-help.html              # Página de ayuda SSL
├── css/                       # Estilos CSS
├── js/                        # JavaScript
├── images/                    # Imágenes
├── LARAVEL/                   # Backend Laravel
├── backup-scripts/            # Scripts de respaldo
└── logs/                      # Logs del sistema
```

## 🔧 Comandos Útiles

### Gestión de Contenedores
```bash
# Iniciar servicios
docker-compose up -d

# Ver logs en tiempo real
docker-compose logs -f

# Detener servicios
docker-compose down

# Reconstruir contenedores
docker-compose up --build -d

# Ver estado de contenedores
docker-compose ps
```

### Desarrollo
```bash
# Acceder al contenedor frontend
docker exec -it gestor_eventos_frontend_ssl sh

# Acceder al contenedor backend
docker exec -it gestor_eventos_backend_ssl bash

# Ver logs específicos
docker logs gestor_eventos_frontend_ssl
docker logs gestor_eventos_backend_ssl
```

## 🌟 Características

### SSL/TLS
- ✅ Certificado SSL auto-firmado
- ✅ Redirección automática HTTP → HTTPS
- ✅ Headers de seguridad modernos
- ✅ TLS 1.2/1.3 habilitado
- ✅ HSTS, CSP, X-Frame-Options

### Frontend
- ✅ Nginx con configuración SSL optimizada
- ✅ Servicio de archivos estáticos
- ✅ Proxy reverso para API
- ✅ Páginas de error personalizadas

### Backend
- ✅ Laravel 8.2 con PHP 8.0
- ✅ API RESTful
- ✅ Base de datos PostgreSQL
- ✅ Sistema de respaldos automáticos

## 🐛 Solución de Problemas

### Error "Connection refused"
```bash
# Verificar que los contenedores estén corriendo
docker-compose ps

# Reiniciar servicios
docker-compose restart
```

### Error "400 Bad Request"
- **Causa:** Acceso incorrecto usando `localhost:443`
- **Solución:** Usar `https://localhost` en su lugar

### Certificado SSL no confiable
- **Normal:** Es un certificado auto-firmado
- **Solución:** Aceptar la advertencia del navegador

## 📞 Soporte

Para problemas o preguntas:
1. Revisar logs: `docker-compose logs -f`
2. Verificar estado: `docker-compose ps`
3. Acceder a la ayuda: https://localhost/ssl-help

---

**🔒 Versión SSL - Gestor de Eventos**  
*Desarrollado con Docker, Nginx, Laravel y PostgreSQL*
