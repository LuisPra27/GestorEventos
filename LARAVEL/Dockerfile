# Railway Backend - Laravel
FROM php:8.2-cli

# Establecer el directorio de trabajo
WORKDIR /var/www/html

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    libzip-dev \
    nginx \
    supervisor \
    postgresql-client \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensiones PHP necesarias
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql \
    && docker-php-ext-install pdo pdo_pgsql pgsql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copiar archivos del proyecto Laravel
COPY . .

# Instalar dependencias PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Crear archivo .env por defecto para Railway
RUN echo 'APP_NAME="Gestor de Eventos"' > .env && \
    echo 'APP_ENV=production' >> .env && \
    echo 'APP_KEY=' >> .env && \
    echo 'APP_DEBUG=false' >> .env && \
    echo 'APP_TIMEZONE=America/Guayaquil' >> .env && \
    echo 'APP_URL=http://localhost' >> .env && \
    echo '' >> .env && \
    echo 'DB_CONNECTION=pgsql' >> .env && \
    echo 'DB_HOST=${PGHOST}' >> .env && \
    echo 'DB_PORT=${PGPORT}' >> .env && \
    echo 'DB_DATABASE=${PGDATABASE}' >> .env && \
    echo 'DB_USERNAME=${PGUSER}' >> .env && \
    echo 'DB_PASSWORD=${PGPASSWORD}' >> .env && \
    echo '' >> .env && \
    echo 'BROADCAST_DRIVER=log' >> .env && \
    echo 'CACHE_DRIVER=file' >> .env && \
    echo 'FILESYSTEM_DISK=local' >> .env && \
    echo 'QUEUE_CONNECTION=sync' >> .env && \
    echo 'SESSION_DRIVER=file' >> .env && \
    echo 'SESSION_LIFETIME=120' >> .env

# Generar clave de aplicación
RUN php artisan key:generate --force

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Crear script de inicio para Railway
RUN echo '#!/bin/bash' > /start.sh && \
    echo 'set -e' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Función para esperar PostgreSQL' >> /start.sh && \
    echo 'wait_for_postgres() {' >> /start.sh && \
    echo '    echo "⏳ Esperando PostgreSQL..."' >> /start.sh && \
    echo '    for i in {1..30}; do' >> /start.sh && \
    echo '        if pg_isready -h $PGHOST -p $PGPORT -U $PGUSER; then' >> /start.sh && \
    echo '            echo "✅ PostgreSQL está listo"' >> /start.sh && \
    echo '            return 0' >> /start.sh && \
    echo '        fi' >> /start.sh && \
    echo '        echo "⏳ Intento $i/30..."' >> /start.sh && \
    echo '        sleep 5' >> /start.sh && \
    echo '    done' >> /start.sh && \
    echo '    echo "❌ PostgreSQL no está disponible después de 150 segundos"' >> /start.sh && \
    echo '    return 1' >> /start.sh && \
    echo '}' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Actualizar variables de entorno desde Railway' >> /start.sh && \
    echo 'if [ ! -z "$PGHOST" ]; then' >> /start.sh && \
    echo '    echo "🔧 Configurando base de datos PostgreSQL..."' >> /start.sh && \
    echo '    sed -i "s/DB_HOST=.*/DB_HOST=${PGHOST}/" .env' >> /start.sh && \
    echo '    sed -i "s/DB_PORT=.*/DB_PORT=${PGPORT}/" .env' >> /start.sh && \
    echo '    sed -i "s/DB_DATABASE=.*/DB_DATABASE=${PGDATABASE}/" .env' >> /start.sh && \
    echo '    sed -i "s/DB_USERNAME=.*/DB_USERNAME=${PGUSER}/" .env' >> /start.sh && \
    echo '    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=${PGPASSWORD}/" .env' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '    # Esperar PostgreSQL' >> /start.sh && \
    echo '    wait_for_postgres' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '    # Ejecutar migraciones' >> /start.sh && \
    echo '    echo "📦 Ejecutando migraciones..."' >> /start.sh && \
    echo '    php artisan migrate:fresh --force || echo "⚠️ Error en migraciones"' >> /start.sh && \
    echo '    php artisan db:seed --force || echo "⚠️ Error en seeders"' >> /start.sh && \
    echo 'else' >> /start.sh && \
    echo '    echo "⚠️ Variables PostgreSQL no encontradas"' >> /start.sh && \
    echo 'fi' >> /start.sh && \
    echo '' >> /start.sh && \
    echo 'if [ ! -z "$RAILWAY_PUBLIC_DOMAIN" ]; then' >> /start.sh && \
    echo '    sed -i "s|APP_URL=.*|APP_URL=https://${RAILWAY_PUBLIC_DOMAIN}|" .env' >> /start.sh && \
    echo 'fi' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Limpiar cache' >> /start.sh && \
    echo 'php artisan config:cache' >> /start.sh && \
    echo 'php artisan route:cache' >> /start.sh && \
    echo 'php artisan view:cache' >> /start.sh && \
    echo '' >> /start.sh && \
    echo '# Iniciar servidor' >> /start.sh && \
    echo 'echo "🚀 Iniciando servidor en puerto ${PORT:-8000}..."' >> /start.sh && \
    echo 'php artisan serve --host=0.0.0.0 --port=${PORT:-8000}' >> /start.sh && \
    chmod +x /start.sh

# Exponer puerto para Railway
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -f http://localhost:${PORT:-8000}/api/health || exit 1

# Comando de inicio
CMD ["/start.sh"]
