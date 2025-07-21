# Event Management System - Laravel Backend API

## Descripción

Este es el backend API en Laravel para el sistema de gestión de eventos. Proporciona una API RESTful para manejar usuarios, servicios, eventos y seguimientos con autenticación basada en roles.

## Características

- **Autenticación API** con Laravel Sanctum
- **Control de acceso basado en roles** (Cliente, Empleado, Gerente)
- **Gestión de eventos** completa con asignación de empleados
- **Sistema de seguimiento** para eventos
- **Gestión de servicios** y usuarios
- **CORS configurado** para frontend separado
- **Compatible con base de datos PostgreSQL existente**

## Roles de Usuario

1. **Cliente (rol_id: 1)**: Puede crear y gestionar sus propios eventos
2. **Empleado (rol_id: 2)**: Puede ver eventos asignados y actualizar estados
3. **Gerente (rol_id: 3)**: Acceso completo a toda la funcionalidad del sistema

## Configuración

### Base de Datos PostgreSQL

El proyecto está configurado para usar la base de datos PostgreSQL existente:

```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=eventos
DB_USERNAME=postgres
DB_PASSWORD=luis123
```

### Tablas Existentes

El sistema utiliza las siguientes tablas existentes:
- `usuarios` (en lugar de `users`)
- `servicios` (en lugar de `services`) 
- `eventos` (en lugar de `events`)
- `roles`
- `seguimientos`

## Instalación

1. **Instalar dependencias**:
   ```bash
   composer install
   ```

2. **Configurar variables de entorno**:
   - El archivo `.env` ya está configurado para PostgreSQL
   - Verificar que las credenciales de base de datos sean correctas

3. **Iniciar servidor de desarrollo**:
   ```bash
   php artisan serve
   ```

   El servidor estará disponible en: `http://localhost:8000`

## Usuarios de Prueba

Puedes usar estos usuarios existentes para probar el sistema:

```json
{
  "admin": {
    "email": "admin@test.com", 
    "password": "admin123",
    "rol": "Gerente"
  },
  "empleado": {
    "email": "empleado@test.com",
    "password": "empleado123", 
    "rol": "Empleado"
  },
  "cliente": {
    "email": "cliente@test.com",
    "password": "cliente123",
    "rol": "Cliente"
  }
}
```

## API Endpoints

### Autenticación
- `POST /api/login` - Iniciar sesión
- `POST /api/logout` - Cerrar sesión (requiere auth)
- `GET /api/me` - Obtener datos del usuario actual (requiere auth)

### Usuarios (Solo Gerentes)
- `GET /api/users` - Listar usuarios
- `POST /api/users` - Crear usuario
- `PUT /api/users/{id}` - Actualizar usuario
- `DELETE /api/users/{id}` - Eliminar usuario
- `PUT /api/users/{id}/toggle-status` - Cambiar estado activo/inactivo

### Servicios
- `GET /api/services` - Listar servicios (todos los roles)
- `POST /api/services` - Crear servicio (solo gerentes)
- `PUT /api/services/{id}` - Actualizar servicio (solo gerentes)
- `DELETE /api/services/{id}` - Eliminar servicio (solo gerentes)

### Eventos
- `GET /api/events` - Listar eventos (filtrados por rol)
- `GET /api/events/{id}` - Ver detalles de evento
- `POST /api/events` - Crear evento (solo clientes)
- `PUT /api/events/{id}` - Actualizar evento
- `PUT /api/events/{id}/assign-employee` - Asignar empleado (solo gerentes)
- `PUT /api/events/{id}/change-status` - Cambiar estado
- `POST /api/events/{id}/seguimientos` - Agregar seguimiento

## Ejemplo de Uso

### Login
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@test.com","password":"admin123"}'
```

### Obtener usuarios (con token)
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Autenticación

El sistema usa Laravel Sanctum para autenticación API. Después del login, incluir el token en el header:

```
Authorization: Bearer {token}
```

## Frontend

Este backend está diseñado para trabajar con un frontend separado ubicado en el directorio padre. Para usar el frontend:

1. El backend debe estar ejecutándose en `http://localhost:8000`
2. Actualizar las URLs del frontend para apuntar a la nueva API Laravel
3. Los archivos HTML están en `../` (directorio padre)

## Migración del Frontend

Para migrar el frontend existente al nuevo backend Laravel:

1. Cambiar las URLs de `backend/api/` a `http://localhost:8000/api/`
2. Actualizar el manejo de autenticación para usar tokens Sanctum
3. Ajustar las respuestas JSON según la nueva estructura

## Desarrollo

El servidor Laravel está configurado para ejecutarse en `http://localhost:8000` y acepta conexiones desde cualquier origen (CORS configurado).

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
