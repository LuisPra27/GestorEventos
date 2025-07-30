-- Script de inicialización de la base de datos del Gestor de Eventos
-- Este script crea todas las tablas y datos iniciales necesarios

-- Tabla: roles
CREATE TABLE roles (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: users
CREATE TABLE users (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(255) NULL,
    rol_id BIGINT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- Tabla: password_reset_tokens
CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

-- Tabla: sessions
CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL
);

-- Índices para sessions
CREATE INDEX sessions_user_id_index ON sessions(user_id);
CREATE INDEX sessions_last_activity_index ON sessions(last_activity);

-- Tabla: cache
CREATE TABLE cache (
    key VARCHAR(255) PRIMARY KEY,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

-- Tabla: cache_locks
CREATE TABLE cache_locks (
    key VARCHAR(255) PRIMARY KEY,
    owner VARCHAR(255) NOT NULL,
    expiration INTEGER NOT NULL
);

-- Tabla: jobs
CREATE TABLE jobs (
    id BIGSERIAL PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload TEXT NOT NULL,
    attempts INTEGER NOT NULL,
    reserved_at INTEGER NULL,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

-- Índices para jobs
CREATE INDEX jobs_queue_index ON jobs(queue);

-- Tabla: job_batches
CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT NULL,
    cancelled_at INTEGER NULL,
    created_at INTEGER NOT NULL,
    finished_at INTEGER NULL
);

-- Tabla: failed_jobs
CREATE TABLE failed_jobs (
    id BIGSERIAL PRIMARY KEY,
    uuid VARCHAR(255) UNIQUE NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: personal_access_tokens
CREATE TABLE personal_access_tokens (
    id BIGSERIAL PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Índices para personal_access_tokens
CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON personal_access_tokens(tokenable_type, tokenable_id);

-- Tabla: services
CREATE TABLE services (
    id BIGSERIAL PRIMARY KEY,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    precio DECIMAL(10,2) NOT NULL,
    duracion_horas INTEGER NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabla: events
CREATE TABLE events (
    id BIGSERIAL PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT NULL,
    cliente_id BIGINT NOT NULL,
    servicio_id BIGINT NOT NULL,
    empleado_id BIGINT NULL,
    fecha_evento TIMESTAMP NOT NULL,
    ubicacion VARCHAR(255) NOT NULL,
    numero_invitados INTEGER DEFAULT 50,
    presupuesto DECIMAL(10,2) NULL,
    notas_especiales TEXT NULL,
    estado VARCHAR(255) DEFAULT 'pendiente' CHECK (estado IN ('pendiente', 'en_progreso', 'completado', 'cancelado')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (cliente_id) REFERENCES users(id),
    FOREIGN KEY (servicio_id) REFERENCES services(id),
    FOREIGN KEY (empleado_id) REFERENCES users(id)
);

-- Tabla: seguimientos
CREATE TABLE seguimientos (
    id BIGSERIAL PRIMARY KEY,
    evento_id BIGINT NOT NULL,
    usuario_id BIGINT NOT NULL,
    comentario TEXT NULL,
    tipo VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (evento_id) REFERENCES events(id),
    FOREIGN KEY (usuario_id) REFERENCES users(id)
);

-- Insertar datos iniciales

-- Roles del sistema
INSERT INTO roles (nombre, descripcion) VALUES 
('cliente', 'Usuario cliente que solicita eventos'),
('empleado', 'Empleado que gestiona y ejecuta eventos'),
('gerente', 'Gerente con acceso completo al sistema');

-- Servicios básicos
INSERT INTO services (nombre, descripcion, precio, duracion_horas, activo) VALUES 
('Boda Completa', 'Organización completa de boda incluyendo decoración, catering y música', 500.00, 8, true),
('Fiesta de Cumpleaños', 'Organización de fiesta de cumpleaños con decoración temática', 800.00, 4, true),
('Evento Corporativo', 'Organización de eventos empresariales y conferencias', 250.00, 6, true),
('Baby Shower', 'Organización de baby shower con decoración y catering', 600.00, 3, true),
('Graduación', 'Celebración de graduación con ceremonia y recepción', 120.00, 5, true),
('Aniversario', 'Celebración de aniversario de bodas', 150.00, 6, true);

-- Usuarios del sistema
INSERT INTO users (nombre, email, password, telefono, rol_id, activo) VALUES 
('Administrador', 'admin@gestor.com', '$2y$10$Bm2duqLzJl5C4MEVz2oc3OkyZ7Gf3P3l0THUVgtcta8XjMFq21Fqa', '555-0001', 3, true),
('Empleado Gestor', 'empleado@gestor.com', '$2y$10$xFEJk62qv/1MTXJLETH1Gekvycrb0A8za7UJRhJ/6pwB59kXNN0IK', '555-0002', 2, true);

-- Credenciales de acceso:
-- Gerente: admin@gestor.com / admin123
-- Empleado: empleado@gestor.com / empleado123

-- Crear función para actualizar timestamps automáticamente
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Crear triggers para actualizar updated_at automáticamente
CREATE TRIGGER update_roles_updated_at BEFORE UPDATE ON roles FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_services_updated_at BEFORE UPDATE ON services FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_events_updated_at BEFORE UPDATE ON events FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();
CREATE TRIGGER update_seguimientos_updated_at BEFORE UPDATE ON seguimientos FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- Crear tabla para migraciones de Laravel (para evitar conflictos)
CREATE TABLE migrations (
    id SERIAL PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INTEGER NOT NULL
);

-- Insertar registros de migraciones como si ya se hubieran ejecutado
INSERT INTO migrations (migration, batch) VALUES 
('0001_01_01_000000_create_roles_table', 1),
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_07_21_060750_create_personal_access_tokens_table', 1),
('2025_07_21_060809_create_services_table', 1),
('2025_07_21_060814_create_events_table', 1),
('2025_07_21_060820_create_seguimientos_table', 1);

-- Confirmación
DO $$
BEGIN
    RAISE NOTICE 'Base de datos inicializada correctamente';
    RAISE NOTICE 'Usuarios creados:';
    RAISE NOTICE '- admin@gestor.com (Gerente) - Contraseña: admin123';
    RAISE NOTICE '- empleado@gestor.com (Empleado) - Contraseña: empleado123';
END $$;
