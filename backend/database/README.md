# Base de Datos - Gestor de Eventos

## Estado
✅ **Schema ejecutado exitosamente en PostgreSQL**
- Base de datos: `eventos`
- Ejecutado en pgAdmin el: ${new Date().toLocaleDateString('es-ES')}

## Estructura de la Base de Datos

### Tablas creadas:
- `roles` - Roles de usuario (cliente, empleado, gerente)
- `usuarios` - Información de usuarios del sistema
- `servicios` - Servicios disponibles para eventos
- `eventos` - Eventos creados por los clientes
- `pagos` - Información de pagos de eventos
- `seguimientos` - Comentarios y seguimiento de eventos

### Usuarios de prueba:
- **Administrador**: admin@eventos.com (password: password)
- **Empleado**: empleado@eventos.com (password: password)

## Notas
- El schema está optimizado para PostgreSQL
- Incluye triggers automáticos para campos `updated_at`
- Las contraseñas están hasheadas con bcrypt
